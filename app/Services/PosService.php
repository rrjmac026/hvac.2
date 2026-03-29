<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\StockMovement;
use Illuminate\Support\Str;

class PosService
{
    /**
     * Core checkout logic — runs inside a DB transaction from the controller.
     *
     * @param  array  $data  Validated request data
     * @return array{invoice: Invoice, change: float}
     */
    public function processCheckout(array $data): array
    {
        // 1. Calculate totals
        $subtotal = collect($data['items'])->sum(
            fn ($item) => $item['quantity'] * $item['unit_price']
        );

        $discount = $data['discount'] ?? 0;
        $tax      = $data['tax'] ?? 0;
        $total    = $subtotal - $discount + $tax;

        // 2. Create Invoice
        $invoice = Invoice::create([
            'client_id'      => $data['client_id'] ?? null,
            'appointment_id' => $data['appointment_id'] ?? null,
            'invoice_number' => $this->generateInvoiceNumber(),
            'issued_at'      => now(),
            'due_date'       => now()->toDateString(),
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total'          => $total,
            'amount_paid'    => 0,
            'status'         => Invoice::STATUS_DRAFT,
            'notes'          => $data['notes'] ?? null,
        ]);

        // 3. Create Invoice Items + deduct stock
        foreach ($data['items'] as $line) {
            $lineSubtotal = $line['quantity'] * $line['unit_price'];

            InvoiceItem::create([
                'invoice_id'        => $invoice->id,
                'inventory_item_id' => $line['inventory_item_id'] ?? null,
                'description'       => $line['description'],
                'quantity'          => $line['quantity'],
                'unit_price'        => $line['unit_price'],
                'subtotal'          => $lineSubtotal,
            ]);

            // Deduct stock for physical items (not services)
            if (! empty($line['inventory_item_id'])) {
                $this->deductStock(
                    inventoryItemId: $line['inventory_item_id'],
                    quantity: $line['quantity'],
                    referenceType: Invoice::class,
                    referenceId: $invoice->id,
                );
            }
        }

        // 4. Record payment
        Payment::create([
            'invoice_id'       => $invoice->id,
            'amount'           => $total,
            'payment_method'   => $data['payment_method'],
            'reference_number' => $data['reference_number'] ?? null,
            'paid_at'          => now(),
        ]);

        // 5. Mark invoice as paid
        $invoice->update([
            'amount_paid' => $total,
            'status'      => Invoice::STATUS_PAID,
        ]);

        // 6. Compute change (cash only)
        $change = 0;
        if ($data['payment_method'] === Payment::METHOD_CASH && isset($data['amount_tendered'])) {
            $change = max(0, $data['amount_tendered'] - $total);
        }

        return compact('invoice', 'change');
    }

    /**
     * Void an invoice and reverse its stock movements.
     */
    public function voidInvoice(Invoice $invoice, string $reason): void
    {
        // Reverse stock for each item
        foreach ($invoice->items as $lineItem) {
            if (! $lineItem->inventory_item_id) {
                continue;
            }

            $item = InventoryItem::lockForUpdate()->find($lineItem->inventory_item_id);

            if ($item) {
                $before = $item->stock_quantity;
                $after  = $before + $lineItem->quantity;

                $item->update(['stock_quantity' => $after]);

                StockMovement::create([
                    'inventory_item_id' => $item->id,
                    'user_id'           => auth()->id(),
                    'type'              => StockMovement::TYPE_IN,
                    'quantity'          => $lineItem->quantity,
                    'quantity_before'   => $before,
                    'quantity_after'    => $after,
                    'reference_type'    => Invoice::class,
                    'reference_id'      => $invoice->id,
                    'notes'             => "Void: {$reason}",
                ]);
            }
        }

        $invoice->update([
            'status' => Invoice::STATUS_CANCELLED,
            'notes'  => trim(($invoice->notes ?? '') . "\nVoided: {$reason}"),
        ]);
    }

    /**
     * Deduct stock for a sold item and log the movement.
     *
     * @throws \Exception if insufficient stock
     */
    private function deductStock(
        int    $inventoryItemId,
        float  $quantity,
        string $referenceType,
        int    $referenceId,
    ): void {
        // Lock the row to prevent race conditions on concurrent sales
        $item = InventoryItem::lockForUpdate()->find($inventoryItemId);

        if (! $item) {
            return;
        }

        // Services don't have stock to deduct
        if ($item->type === InventoryItem::TYPE_SERVICE) {
            return;
        }

        if ($item->stock_quantity < $quantity) {
            throw new \Exception("Insufficient stock for {$item->name}. Available: {$item->stock_quantity} {$item->unit}.");
        }

        $before = $item->stock_quantity;
        $after  = $before - $quantity;

        $item->update(['stock_quantity' => $after]);

        StockMovement::create([
            'inventory_item_id' => $item->id,
            'user_id'           => auth()->id(),
            'type'              => StockMovement::TYPE_OUT,
            'quantity'          => $quantity,
            'quantity_before'   => $before,
            'quantity_after'    => $after,
            'reference_type'    => $referenceType,
            'reference_id'      => $referenceId,
            'notes'             => 'POS sale',
        ]);
    }

    /**
     * Generate a unique invoice number like INV-20250329-0042.
     */
    private function generateInvoiceNumber(): string
    {
        $date     = now()->format('Ymd');
        $sequence = Invoice::whereDate('issued_at', now()->toDateString())->count() + 1;

        return 'INV-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
