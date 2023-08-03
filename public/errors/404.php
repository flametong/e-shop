<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Page not found</title>
    <style type="text/css">
        html {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .wrap {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo {
            width: 430px;
        }

        .link-back {
            margin-top: 25px;
            display: flex;
            justify-content: center;
        }

        .link-back a {
            color: #eee;
            font-size: 20px;
            padding: 5px 10px;
            background: #FF3366;
            text-decoration: none;
            -webkit-border-radius: .3em;
            -moz-border-radius: .3em;
            border-radius: .3em;
        }

        .link-back a:hover {
            color: #fff;
        }

    </style>
</head>
<body>
<div class="wrap">
    <div class="logo">
        <img src="/errors/images/404.png" alt=""  />
        <div class="link-back">
            <a href="<?= PATH ?>">
                Return to Home
            </a>
        </div>
    </div>
</div>
</body>
</html>

