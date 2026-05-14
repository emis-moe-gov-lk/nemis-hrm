<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 | Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto;
            background: #f8fafc;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card {
            background: #ffffff;
            padding: 3rem 2rem;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .svg-illustration {
            width: 100%;
            max-width: 240px;
            height: auto;
            margin: 0 auto 2rem;
        }
        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.75rem;
            color: #2563eb; /* Blue Accent */
        }
        p {
            font-size: 1.05rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #2563eb; /* Blue Accent */
            color: #ffffff;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.875rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="svg-illustration">
        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
            <circle cx="200" cy="150" r="100" fill="#eff6ff" />
            <rect x="160" y="70" width="80" height="110" rx="8" fill="#ffffff" stroke="#3b82f6" stroke-width="4" />
            <g transform="translate(215, 170) rotate(-10)">
                <circle cx="0" cy="0" r="30" fill="#ffffff" stroke="#1e40af" stroke-width="5" />
                <line x1="22" y1="22" x2="45" y2="45" stroke="#1e40af" stroke-width="10" stroke-linecap="round" />
                <text x="-21" y="8" font-family="sans-serif" font-size="20" font-weight="900" fill="#1e40af">404</text>
            </g>
        </svg>
    </div>
    <h1>404</h1>
    <h1>Page Not Found</h1>
    <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <div class="actions">
        <button class="btn btn-secondary" onclick="history.back()">Go Back</button>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
    </div>
    <div class="footer">© {{ date('Y') }} National Education System. All Rights Reserved.</div>
</div>
</body>
</html>