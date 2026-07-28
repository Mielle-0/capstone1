<div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
    <h2>Hello {{ $firstName }},</h2>
    <p>Your verification code is:</p>
    <div style='margin: 20px 0;'>
        <span style='font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #0d6efd; background: #f8f9fa; padding: 10px 20px; border: 1px dashed #ccc; border-radius: 5px;'>
            {{ $verificationCode }}
        </span>
    </div>
    <p>Please enter this code back on the form to proceed.</p>
</div>
