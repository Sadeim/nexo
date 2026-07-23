<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXO POS — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('pos_assets/css/pos.css') }}" rel="stylesheet">
</head>
<body class="pos-body">
    <div class="pos-login-wrap">
        <form class="pos-login-card" method="POST" action="{{ route('pos.login.post') }}">
            @csrf
            <h1>NEXO POS</h1>
            <p class="sub">Sign in to start a session</p>

            @if ($errors->any())
                <div class="pos-error">{{ $errors->first() }}</div>
            @endif

            <div class="pos-field">
                <label for="email">Email</label>
                <input class="pos-input" type="email" id="email" name="email"
                       value="{{ old('email') }}" autofocus autocomplete="email" required>
            </div>

            <div class="pos-field">
                <label for="password">Password</label>
                <input class="pos-input" type="password" id="password" name="password"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="pos-btn pos-btn-primary">Sign in</button>
        </form>
    </div>
</body>
</html>
