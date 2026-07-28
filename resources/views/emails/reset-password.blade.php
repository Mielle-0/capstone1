<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn-maroon { display: inline-block; padding: 12px 24px; background-color: #be0002; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; margin-bottom: 15px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello, {{ $user->usr_name }}!</h2>
        
        <p>You are receiving this email because we received a password reset request for your Staff Portal account.</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="btn-maroon">Reset Password</a>
        </div>
        
        <p>If the button above does not work, copy and paste the following link into your browser:</p>
        <p><a href="{{ $resetUrl }}" style="color: #be0002; word-break: break-all;">{{ $resetUrl }}</a></p>
        
        <p>If you did not request a password reset, no further action is required.</p>
        
        <div class="footer">
            <p>Regards,<br>The Staff Portal Team</p>
        </div>
    </div>
</body>
</html>