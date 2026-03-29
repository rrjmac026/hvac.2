<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Highland Vets Animal Clinic</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:700,800,900|dm-sans:300,400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g-deep:  #1B4332;
            --g-mid:   #2D6A4F;
            --g-light: #52B788;
            --lime:    #A8E800;
            --lime2:   #C5F500;
            --cream:   #F5F8F0;
            --white:   #FFFFFF;
            --ink:     #111811;
            --muted:   #4A6058;
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--ink); -webkit-font-smoothing: antialiased; }
        .serif { font-family: 'Playfair Display', serif; }

        /* ── Animations ── */
        @keyframes fadeUp   { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:translateY(0); } }
        @keyframes popIn    { 0% { opacity:0; transform:scale(.86); } 60% { transform:scale(1.04); } 100% { opacity:1; transform:scale(1); } }
        @keyframes float    { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-12px); } }
        @keyframes spin     { to { transform:rotate(360deg); } }

        .a1 { animation: fadeUp .6s ease both .05s; }
        .a2 { animation: fadeUp .6s ease both .18s; }
        .a3 { animation: fadeUp .6s ease both .30s; }
        .a4 { animation: fadeUp .6s ease both .42s; }
        .a5 { animation: fadeUp .6s ease both .54s; }
        .pop { animation: popIn .75s cubic-bezier(.34,1.56,.64,1) both .2s; }

        /* ── Navbar ── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(245,248,240,.9);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(27,67,50,.08);
        }
        .nav-inner {
            max-width: 1120px; margin: 0 auto;
            padding: 0 1.5rem; height: 68px;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .nav-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .nav-brand img { width:40px; height:40px; object-fit:contain; }
        .brand-name { font-family:'Playfair Display',serif; font-weight:900; font-size:1rem; color:var(--g-deep); line-height:1; }
        .brand-name em { font-style:normal; color:var(--lime); }
        .brand-sub { font-size:.58rem; letter-spacing:.14em; text-transform:uppercase; color:var(--g-mid); font-weight:600; margin-top:2px; }

        .nav-links { display:flex; align-items:center; gap:2rem; list-style:none; }
        .nav-links a { font-size:.875rem; font-weight:500; color:var(--muted); text-decoration:none; transition:color .2s; }
        .nav-links a:hover { color:var(--g-deep); }

        .btn-ghost {
            font-size:.875rem; font-weight:600; color:var(--g-deep); text-decoration:none;
            padding:.44rem 1.1rem; border-radius:9999px;
            border:1.5px solid rgba(27,67,50,.2); transition:border-color .2s, background .2s;
        }
        .btn-ghost:hover { border-color:var(--g-deep); background:rgba(27,67,50,.05); }
        .btn-solid {
            font-size:.875rem; font-weight:700; color:var(--g-deep);
            background:var(--lime); text-decoration:none;
            padding:.46rem 1.25rem; border-radius:9999px; transition:background .2s, transform .15s;
        }
        .btn-solid:hover { background:var(--lime2); transform:translateY(-1px); }

        /* ── Hero ── */
        .hero {
            max-width: 1120px; margin: 0 auto;
            padding: 5rem 1.5rem 4rem;
            min-height: calc(100vh - 68px);
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 4rem;
        }

        .pill {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(27,67,50,.07); border:1px solid rgba(27,67,50,.14);
            color:var(--g-deep); border-radius:9999px;
            padding:.28rem .9rem;
            font-size:.72rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase;
        }
        .pill-dot { width:6px; height:6px; border-radius:50%; background:var(--lime); display:inline-block; }

        .hero-title {
            font-family:'Playfair Display',serif; font-weight:900;
            font-size:clamp(2.5rem,5vw,4rem); line-height:1.06;
            color:var(--ink); margin-top:1.2rem;
        }
        .underline-lime {
            position:relative; display:inline-block; color:var(--g-deep);
        }
        .underline-lime::after {
            content:''; position:absolute; left:0; bottom:-4px;
            width:100%; height:5px; border-radius:9999px;
            background:linear-gradient(90deg,var(--lime),#4de000);
        }

        .hero-sub { margin-top:1.4rem; font-size:1.05rem; line-height:1.75; color:var(--muted); max-width:44ch; }

        .hero-actions { margin-top:2.2rem; display:flex; align-items:center; gap:.8rem; flex-wrap:wrap; }
        .cta-primary {
            display:inline-flex; align-items:center; gap:8px;
            background:var(--g-deep); color:var(--lime);
            font-size:.92rem; font-weight:700;
            padding:.82rem 1.8rem; border-radius:9999px; text-decoration:none;
            transition:transform .2s, box-shadow .2s;
            box-shadow:0 6px 24px rgba(27,67,50,.28);
        }
        .cta-primary:hover { transform:translateY(-2px); box-shadow:0 10px 32px rgba(27,67,50,.38); }
        .cta-primary svg { transition:transform .2s; }
        .cta-primary:hover svg { transform:translateX(3px); }
        .cta-secondary {
            display:inline-flex; align-items:center; gap:8px;
            background:transparent; color:var(--g-deep);
            font-size:.92rem; font-weight:600;
            padding:.82rem 1.6rem; border-radius:9999px; text-decoration:none;
            border:1.5px solid rgba(27,67,50,.2); transition:border-color .2s, background .2s;
        }
        .cta-secondary:hover { border-color:var(--g-deep); background:rgba(27,67,50,.04); }

        /* stats */
        .stats-row { margin-top:3rem; display:flex; gap:1rem; flex-wrap:wrap; }
        .stat-box {
            background:white; border:1px solid rgba(27,67,50,.09);
            border-radius:1rem; padding:1rem 1.4rem; min-width:100px;
        }
        .stat-num { font-family:'Playfair Display',serif; font-weight:900; font-size:1.9rem; color:var(--g-deep); line-height:1; }
        .stat-lbl { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); margin-top:4px; }

        /* ── Logo side ── */
        .hero-right { display:flex; align-items:center; justify-content:flex-end; }
        .logo-scene { position:relative; display:flex; align-items:center; justify-content:center; }

        /* glow blob behind everything */
        .logo-glow {
            position:absolute; inset:-40px; border-radius:50%;
            background:radial-gradient(circle, rgba(168,232,0,.2), transparent 68%);
            pointer-events:none;
        }

        /* spinning ring */
        .logo-ring {
            width:340px; height:340px; border-radius:50%;
            background:conic-gradient(from 0deg, #A8E800, #4de000, #c8f000, #A8E800);
            display:flex; align-items:center; justify-content:center;
            animation:spin 14s linear infinite;
        }
        .logo-ring-inner {
            width:324px; height:324px; border-radius:50%;
            background:var(--cream);
            display:flex; align-items:center; justify-content:center;
        }
        .logo-img {
            width:268px; height:268px; object-fit:contain;
            animation:float 5s ease-in-out infinite;
            position:relative; z-index:1;
        }

        /* decorative dots */
        .dot {
            position:absolute; border-radius:50%; background:var(--lime);
        }

        /* ── Features ── */
        .features { background:white; padding:5rem 1.5rem; }
        .features-inner { max-width:1120px; margin:0 auto; }

        .section-eyebrow {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(27,67,50,.07); border:1px solid rgba(27,67,50,.12);
            color:var(--g-mid); border-radius:9999px;
            padding:.28rem .9rem; font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
        }
        .section-title {
            font-family:'Playfair Display',serif; font-weight:900;
            font-size:clamp(1.8rem,3vw,2.5rem); color:var(--ink);
            margin-top:1rem; line-height:1.15;
        }
        .section-title span { color:var(--g-deep); }

        .features-grid {
            margin-top:3rem;
            display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:1.2rem;
        }
        .feat-card {
            border:1px solid rgba(27,67,50,.08); border-radius:1.25rem;
            padding:1.75rem; background:var(--cream);
            transition:transform .22s, box-shadow .22s;
        }
        .feat-card:hover { transform:translateY(-4px); box-shadow:0 12px 36px rgba(27,67,50,.1); }
        .feat-icon {
            width:46px; height:46px; border-radius:.75rem;
            background:linear-gradient(135deg,var(--lime),#4de000);
            display:flex; align-items:center; justify-content:center; font-size:1.3rem;
        }
        .feat-title { font-weight:700; font-size:1rem; color:var(--ink); margin-top:1rem; }
        .feat-desc { font-size:.875rem; line-height:1.7; color:var(--muted); margin-top:.4rem; }

        /* ── CTA Banner ── */
        .cta-wrap { padding:2.5rem 1.5rem; background:var(--cream); }
        .cta-box {
            max-width:1120px; margin:0 auto;
            background:var(--g-deep); border-radius:2rem;
            padding:4rem 3rem;
            display:flex; align-items:center; justify-content:space-between;
            gap:2rem; flex-wrap:wrap; position:relative; overflow:hidden;
        }
        .cta-box::before {
            content:''; position:absolute; top:-60px; right:-60px;
            width:300px; height:300px; border-radius:50%;
            background:radial-gradient(circle,rgba(168,232,0,.12),transparent 65%);
        }
        .cta-eyebrow { font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--lime); opacity:.85; }
        .cta-h2 { font-family:'Playfair Display',serif; font-size:clamp(1.6rem,3vw,2.2rem); font-weight:900; color:white; margin-top:.5rem; line-height:1.2; }
        .cta-p { font-size:.92rem; color:rgba(255,255,255,.6); margin-top:.6rem; max-width:44ch; line-height:1.65; }
        .cta-btns { display:flex; align-items:center; gap:.75rem; flex-shrink:0; flex-wrap:wrap; position:relative; z-index:1; }
        .cta-lime-btn {
            display:inline-flex; align-items:center; gap:6px;
            background:var(--lime); color:var(--g-deep); font-weight:700; font-size:.9rem;
            padding:.8rem 1.8rem; border-radius:9999px; text-decoration:none;
            transition:background .2s, transform .15s; box-shadow:0 4px 20px rgba(168,232,0,.3);
        }
        .cta-lime-btn:hover { background:var(--lime2); transform:translateY(-2px); }
        .cta-ghost-btn {
            display:inline-flex; align-items:center; gap:6px;
            background:transparent; color:white; font-weight:600; font-size:.9rem;
            padding:.8rem 1.6rem; border-radius:9999px; text-decoration:none;
            border:1.5px solid rgba(255,255,255,.25); transition:border-color .2s, background .2s;
        }
        .cta-ghost-btn:hover { border-color:rgba(255,255,255,.55); background:rgba(255,255,255,.06); }

        /* ── Footer ── */
        footer { background:var(--g-deep); color:rgba(255,255,255,.5); padding:2.5rem 1.5rem; }
        .footer-inner {
            max-width:1120px; margin:0 auto;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;
            font-size:.82rem;
        }
        .footer-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .footer-brand img { width:30px; height:30px; object-fit:contain; opacity:.85; }
        .footer-brand-name { font-family:'Playfair Display',serif; font-weight:900; font-size:.95rem; color:white; }
        .footer-brand-name em { font-style:normal; color:var(--lime); }
        .footer-links { display:flex; gap:1.5rem; }
        .footer-links a { color:rgba(255,255,255,.45); text-decoration:none; transition:color .2s; }
        .footer-links a:hover { color:white; }

        /* ── Responsive ── */
        @media (max-width: 820px) {
            .hero { grid-template-columns:1fr; text-align:center; padding:3rem 1.5rem; gap:3rem; }
            .hero-right { order:-1; justify-content:center; }
            .stats-row, .hero-actions { justify-content:center; }
            .hero-sub { margin-inline:auto; }
            .nav-links { display:none; }
            .logo-ring { width:260px; height:260px; }
            .logo-ring-inner { width:246px; height:246px; }
            .logo-img { width:200px; height:200px; }
            .cta-box { flex-direction:column; text-align:center; padding:3rem 2rem; }
            .cta-btns { justify-content:center; }
        }
    </style>
</head>
<body>

{{-- ── NAVBAR ── --}}
<nav class="navbar">
    <div class="nav-inner">
        <a href="#" class="nav-brand">
            <img src="{{ asset('assets/app_logo.png') }}" alt="Highland Vets">
            <div>
                <div class="brand-name">HIGHLAND <em>VETS</em></div>
                <div class="brand-sub">Animal Clinic</div>
            </div>
        </a>

        <ul class="nav-links">
            <li><a href="#">Services</a></li>
            <li><a href="#">Our Vets</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>

        <div style="display:flex;align-items:center;gap:.6rem;">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-solid">Dashboard →</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-solid">Get Started</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

{{-- ── HERO ── --}}
<section>
    <div class="hero">

        {{-- Left --}}
        <div>
            <div class="a1">
                <span class="pill">
                    <span class="pill-dot"></span>
                    Trusted Animal Care · Est. 2018
                </span>
            </div>

            <h1 class="hero-title a2">
                Your Pet Deserves<br>
                <span class="underline-lime">Highland Care</span>
            </h1>

            <p class="hero-sub a3">
                Compassionate veterinary medicine for dogs, cats, and small animals.
                Book appointments, access records, and stay connected — all in one place.
            </p>

            <div class="hero-actions a4">
                <a href="{{ Route::has('register') ? route('register') : '#' }}" class="cta-primary">
                    Book an Appointment
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="#features" class="cta-secondary">Learn More</a>
            </div>

            <div class="stats-row a5">
                <div class="stat-box">
                    <div class="stat-num">2K+</div>
                    <div class="stat-lbl">Happy Pets</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">8+</div>
                    <div class="stat-lbl">Expert Vets</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">6 yrs</div>
                    <div class="stat-lbl">In Service</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">98%</div>
                    <div class="stat-lbl">Satisfaction</div>
                </div>
            </div>
        </div>

        {{-- Right — Actual Logo --}}
        <div class="hero-right pop">
            <div class="logo-scene">
                <div class="logo-glow"></div>

                {{-- floating decorative dots --}}
                <div class="dot" style="width:10px;height:10px;top:28px;right:44px;animation:float 4s ease-in-out infinite;opacity:.55;"></div>
                <div class="dot" style="width:6px;height:6px;bottom:48px;left:28px;animation:float 6s ease-in-out infinite 1s;opacity:.3;"></div>
                <div class="dot" style="width:14px;height:14px;bottom:70px;right:18px;animation:float 5s ease-in-out infinite .5s;opacity:.2;"></div>

                <div class="logo-ring">
                    <div class="logo-ring-inner">
                        <img
                            src="{{ asset('assets/app_logo.png') }}"
                            alt="Highland Vets Animal Clinic"
                            class="logo-img"
                        >
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ── FEATURES ── --}}
<section class="features" id="features">
    <div class="features-inner">
        <div style="text-align:center;">
            <span class="section-eyebrow">What We Offer</span>
            <h2 class="section-title">Everything your pet needs,<br><span>all in one clinic.</span></h2>
        </div>

        <div class="features-grid">
            <div class="feat-card">
                <div class="feat-icon">🏥</div>
                <div class="feat-title">Medical Records</div>
                <div class="feat-desc">Full visit history, diagnoses, lab results, and prescriptions — securely stored and accessible anytime.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">📅</div>
                <div class="feat-title">Easy Scheduling</div>
                <div class="feat-desc">Book, reschedule, or cancel appointments online. SMS and email reminders keep you on track.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">💉</div>
                <div class="feat-title">Vaccination Tracking</div>
                <div class="feat-desc">Stay on top of your pet's vaccine schedule with automated reminders for upcoming doses.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">💊</div>
                <div class="feat-title">Digital Prescriptions</div>
                <div class="feat-desc">Electronic prescriptions with dosage, frequency, and refill tracking — no more paper slips.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">🧾</div>
                <div class="feat-title">Billing & Invoicing</div>
                <div class="feat-desc">Transparent invoices and multiple payment options — cash, GCash, Maya, and card accepted.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon">💬</div>
                <div class="feat-title">Direct Messaging</div>
                <div class="feat-desc">Chat directly with your vet for quick follow-ups, advice, and pet health updates.</div>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA BANNER ── --}}
<div class="cta-wrap">
    <div class="cta-box">
        <div style="position:relative;z-index:1;">
            <div class="cta-eyebrow">🐾 Client Portal</div>
            <div class="cta-h2">Ready to bring your pet in?</div>
            <div class="cta-p">Create your account and get instant access to online booking, your pet's health timeline, and direct vet messaging.</div>
        </div>
        <div class="cta-btns">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="cta-lime-btn">Create Free Account →</a>
            @endif
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="cta-ghost-btn">Sign In</a>
            @endif
        </div>
    </div>
</div>

{{-- ── FOOTER ── --}}
<footer>
    <div class="footer-inner">
        <a href="#" class="footer-brand">
            <img src="{{ asset('assets/app_logo.png') }}" alt="Logo">
            <span class="footer-brand-name">HIGHLAND <em>VETS</em></span>
        </a>
        <div>© {{ date('Y') }} Highland Vets Animal Clinic. All rights reserved.</div>
        <div class="footer-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Contact</a>
        </div>
    </div>
</footer>

</body>
</html>