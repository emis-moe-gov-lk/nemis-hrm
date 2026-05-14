<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 | Access Denied</title>
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
            color: #dc2626; /* Red Accent */
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
            background: #2563eb; /* Red Accent */
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
            <circle cx="200" cy="150" r="100" fill="#fef2f2" />
            <path d="M200,60 L270,90 C270,160 240,220 200,240 C160,220 130,160 130,90 L200,60 Z" fill="#ffffff" stroke="#ef4444" stroke-width="5" />
            <circle cx="200" cy="140" r="15" fill="none" stroke="#ef4444" stroke-width="4" />
            <rect x="195" y="155" width="10" height="20" rx="2" fill="#ef4444" />
            <text x="200" y="225" font-family="sans-serif" font-size="18" font-weight="900" fill="#ef4444" text-anchor="middle">403</text>
        </svg>
    </div>
    <h1>403</h1>
    <h1>Access Denied</h1>
    <p>You do not have the necessary permissions to access this page. Please contact your administrator if you think this is an error.</p>
    <div class="actions">
        <button class="btn btn-secondary" onclick="history.back()">Go Back</button>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
    </div>
    <div class="footer">© {{ date('Y') }} National Education System. All Rights Reserved.</div>
</div>
</body>
</html>