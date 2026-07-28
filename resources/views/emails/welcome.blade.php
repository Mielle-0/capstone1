<h2>Welcome, {{ $user->usr_name }}!</h2>
<p>An account has been created for you. To finish setting up your account, please click the button below to choose a password.</p>

<a href="{{ $signedUrl }}" style="display:inline-block; padding:10px 20px; background-color:#800000; color:#fff; text-decoration:none; border-radius:5px;">
    Set My Password
</a>

<p><em>This link will expire in 24 hours.</em></p>