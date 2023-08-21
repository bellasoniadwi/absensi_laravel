<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #f4f4f4;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 600px; /* Sesuaikan dengan lebar yang Anda inginkan */
            height: 300px; /* Sesuaikan dengan tinggi yang Anda inginkan */
        }

        .title {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .fcc-btn {
            background-color: #ff0000;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-weight: bold;
            border-radius: 10px; /* Rounded */
            transition: background-color 0.3s ease;
        }

        .fcc-btn:hover {
            background-color: #cc0000;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="icon">
            <span>&#9888;</span> <!-- Unicode character for exclamation mark -->
        </div>
        <div class="title">
            Oops, Kamu tidak memiliki akses pada sistem ini
        </div>
        <a class="fcc-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Keluar') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>
</body>
</html>
