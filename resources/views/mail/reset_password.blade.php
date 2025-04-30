<!DOCTYPE html>
<html>

<head>
    <title>Verfiy your email address</title>
    <meta charset="utf-8" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: #eee;
            font-family: 'Montserrat', sans-serif;
        }

        .emailContainer {
            background-color: #fff;
            width: 70%;
            margin: 50px auto;
            border-radius: 10px;
        }

        .header {
            padding: 40px 50px;
            border-bottom: 1px solid #eee;
        }

        .header .image {
            width: 90.72px;
            height: 100.46px;
            margin: 0 auto;
        }

        .header .image img {
            width: 100%;
        }

        .emailContainer .emailbody {
            padding: 20px 50px;
            text-align: center;
        }

        .emailContainer .emailbody .title {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .emailContainer .emailbody .userName {
            font-weight: 600;
            font-size: 1.1rem;
            display: block;
            text-align: center;
        }

        .emailContainer .emailbody .emailText {
            font-size: 1rem;
            font-weight: normal;
            text-align: center;

        }

        .emailContainer .emailbody a {
            color: #0063C5;
            text-decoration: none;
            font-size: 0.9rem;
            word-wrap: break-word;
        }

        .emailContainer .emailbody .thanks {
            margin-top: 30px;
        }

        .emailContainer .emailbody .helpCenter {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        p.verificationCode {
            text-align: center;
            background: #F3F5F7;
            width: 40%;
            height: 50px;
            line-height: 50px;
            margin: 10px auto;
            letter-spacing: 4px;
            font-weight: bold;
            font-size: 1.2rem;
            border-radius: 10px;
        }

        .btn {
            background: #48a8a6;
            color: #fff;
            width: 35%;
            height: 52px;
            margin: 0 auto;
            border-radius: 50px;
            border: 0;
            font-size: 17px;
            cursor: pointer;
        }
    </style>
    @if ($data['lang'] == 'ar')
        <link href="{{ asset('admin_assets/css/style.rtl.css') }}" rel="stylesheet" type="text/css" />
        <style>
            body {
                direction: rtl;
            }

            .left {
                text-align: right !important;
            }
        </style>
    @endif
</head>

<body>
    <div class="emailContainer">
        <div class="header">
            <div class="image">
                <img src="{{ asset('admin_assets/img/email.png') }}">
            </div>
        </div>
        <div class="emailbody">
            <h1 class="title">{{ __('auth.email.verify_your_account') }}</h1>
            <span class="userName">{{ __('auth.email.hello') }} {{ $data['name'] }}</span>
            <p class="emailText">{{ __('auth.email.we_have_recived_request') }}</p>
            <a href="{{ $data['link'] }}">
                <button class="btn btn-primary">{{ __('auth.email.click_here') }}</button>
            </a>
            <div class="left">
                <p class="thanks">
                    {{ __('auth.email.thank_you_for_using') }}
                    <a href="{{ url('/') }}">{{ __('auth.email.website_name') }}</a>
                </p>
                <p>{{ __('auth.email.regards') }}</p>
                <span class="signature">{{ __('auth.email.website_name') }}</span>
            </div>
        </div>
    </div>
</body>

</html>
