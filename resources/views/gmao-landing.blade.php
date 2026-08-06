<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'GMAO Clinique') }}</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Fallback minimal Tailwind-like styles */
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
                background: linear-gradient(135deg, #fff5f2 0%, #ffffff 100%);
                color: #1b1b18;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
            }
            .card {
                background: #ffffff;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(245, 48, 3, 0.12);
                max-width: 520px;
                width: 100%;
                padding: 48px;
                text-align: center;
            }
            .logo {
                width: 72px;
                height: 72px;
                background: #f53003;
                border-radius: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 24px;
                color: white;
            }
            h1 { font-size: 2rem; font-weight: 700; margin-bottom: 12px; }
            .subtitle { color: #706f6c; font-size: 1.05rem; margin-bottom: 32px; line-height: 1.6; }
            .buttons { display: flex; flex-direction: column; gap: 12px; }
            .btn {
                display: block;
                padding: 14px 24px;
                border-radius: 10px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }
            .btn:hover { transform: translateY(-2px); }
            .btn-primary { background: #f53003; color: #fff; box-shadow: 0 8px 24px rgba(245, 48, 3, 0.25); }
            .btn-secondary { background: #fff; color: #1b1b18; border: 1.5px solid #e3e3e0; }
            .divider { margin: 28px 0; border-top: 1px solid #e3e3e0; }
            .hint { font-size: 0.85rem; color: #706f6c; }
            .hint strong { color: #1b1b18; }
        </style>
    @endif
</head>
<body>
    <div class="card">
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
        </div>

        <h1>GMAO Clinique</h1>
        <p class="subtitle">
            Gestion de Maintenance Assistée par Ordinateur dédiée à la clinique.<br>
            Suivez les équipements médicaux, planifiez les interventions et signalez les pannes en temps réel.
        </p>

        <div class="buttons">
                <a href="/admin" class="btn btn-primary">
                    🔧 Espace Administrateur / Technicien
                </a>
                <a href="/service" class="btn btn-secondary">
                    🚨 Espace Chef de service — Signaler une panne
                </a>
        </div>

        <div class="divider"></div>

        <p class="hint">
            Comptes de démonstration : <br>
            <strong>admin@gmao.local</strong> / <strong>tech@gmao.local</strong> / <strong>agent@gmao.local</strong><br>
            Mot de passe : <strong>password</strong>
        </p>
    </div>
</body>
</html>
