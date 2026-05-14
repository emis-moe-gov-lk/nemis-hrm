<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>

<body style="font-family: sans-serif; background-color: #f5f5f5; padding: 20px;">
    <div
        style="max-width: 600px; background: #fff; border-radius: 8px; padding: 20px; margin: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">Hello!</h2>
        <p>Your password has been reset successfully. Here is your new password:</p>

        <p style="font-size: 20px; font-weight: bold; color: #2b6cb0;">{{ $password }}</p>

        <p>Please log in using this password and change it immediately for your security.</p>
        <br>
        <p>Thank you,<br>{{ config('app.name') }}</p>
    </div>
</body>

</html>
