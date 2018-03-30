<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Reset Password</title>
</head>
<body>
<p>
    Hi, {{ (isset($firstName)) ? $firstName : '' }} {{(isset($lastName)) ? $lastName : '' }}
</p>
<p>
    You have requested for reset your password. please click on below link to reset your password.
</p>
<p>
    Click <a href='{{ isset($url) ? $url : '#' }}'> here </a> to reset password
</p>
</body>
</html>