<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            background-color: #dde0e2;
            padding: 40px 0;
            margin: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .logo-bar {
            width: 100%;
            max-width: 600px;
            margin: 0 auto 0 auto;
        }

        .logo-bar img {
            width: 100%;
            height: auto;
            display: block;
        }

        .wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .content {
            padding: 20px 40px 40px 40px;
            color: #4f5660;
            font-size: 16px;
            line-height: 1.5;
        }

        .content h1 {
            color: #060607;
            font-size: 24px;
            margin-top: 0;
        }

        .footer {
            padding: 30px;
            text-align: center;
            color: #747f8d;
            font-size: 12px;
        }

        .footer a {
            color: #dde0e2;
            text-decoration: none;
            background: none;
        }

        .divider {
            height: 1px;
            background-color: #ebedef;
            margin: 30px 0;
        }
    </style>
</head>

<body>
    <div class="logo-bar">
        <img src="{{ url('https://i.ibb.co/ynDyCJXT/header.jpg') }}" alt="ICS Logo">
    </div>
    <div class="wrapper">
        <div class="content">
            @yield('content')
        </div>
    </div>

    <div class="footer">
        <p>Sent by APPSDEV TCD Portal</a></p>
        <p style="font-weight: bold;">** This is a system generated message. DO NOT REPLY TO THIS EMAIL. **</p>
        <p>Limketkai Building, Ortigas Ave, San Juan City, 1502 Metro Manila</p>
    </div>
</body>

</html>