<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
</head>
<body>
    <p>Hello {{ $user->first_name }},</p>
    <p>Your verification code is: <strong>{{ $user->otp }}</strong></p>
    <p>This code is valid for 10 minutes.</p>
    <p>Thank you for registering.</p>    
</body>
</html>
