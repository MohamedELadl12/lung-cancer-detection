<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            width: 50%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .description {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .code {
            font-size: 24px;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="title">Email Verification</div>
    <div class="description">
        Please enter the verification code sent to your email it's valid for 5 minutes.
    </div>
    <div class="code">{{ $otp }}</div>
</div>
</body>
</html>
