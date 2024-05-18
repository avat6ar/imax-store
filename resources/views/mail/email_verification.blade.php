<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
  <title>Imax Stroe - @lang('emailVerification.title')</title>
  <style>
    /* General styles */
    body {
      font-family: "Cairo", sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 540px;
      background: white;
      margin: auto;
    }

    img.banner {
      width: 127px;
      height: 93px;
      border-radius: 100%;
      display: block;
      margin: 0 auto;
    }

    .content {
      padding: 20px;
      text-align: center;
    }

    .button {
      background: #1577EB;
      border-radius: 8px;
      padding: 12px 20px;
      display: inline-block;
      color: white;
      font-size: 16px;
      font-weight: 700;
      text-decoration: none;
      margin-top: 20px;
    }

    .social-links {
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
    }

    .social-link {
      background: rgba(21, 119, 235, 0.13);
      border-radius: 8px;
      padding: 12px 20px;
      color: #1577EB;
      font-size: 16px;
      font-weight: 700;
    }

    .contact-info {
      color: rgba(0, 0, 0, 0.60);
      font-size: 16px;
      font-weight: 400;
      margin-top: 20px;
    }

    .contact-info strong {
      color: #1577EB;
      font-weight: 700;
    }

    hr {
      border-top: 1px solid rgba(0, 0, 0, 0.12);
    }

    /* Responsive styles */
    @media screen and (max-width: 540px) {
      .container {
        max-width: 100%;
      }

      .content {
        padding: 10px;
      }

      .button {
        margin-top: 10px;
      }

      .social-links {
        flex-wrap: wrap;
        justify-content: center;
      }

      .social-link {
        margin-top: 10px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <img class="banner" src="{{ asset('/logo.png') }}" />
    <div class="content">
      <div><span style="color: black; font-size: 20px; font-weight: 400;">@lang('emailVerification.greeting') {{ $user->name }}
          👋</span></div>
      <div style="margin-top: 20px;">
        <span style="color: rgba(0, 0, 0, 0.60); font-size: 18px; font-weight: 400;">@lang('emailVerification.thanks')</span>
        <span style="color: #1577EB; font-size: 18px; font-weight: 700;">{{ $code->code }}</span>
      </div>
      <div style="margin-top: 20px; font-size: 24px; font-weight: bold;">@lang('emailVerification.verification_code') <span
          style="color: #1577EB;">{{ $code->code }}</span></div>
      <div style="margin-top: 20px;">
        <span style="color: rgba(0, 0, 0, 0.60); font-size: 18px; font-weight: 400;">@lang('emailVerification.ignore_email')</span>
      </div>
      <div class="social-links">
        <a href="{{ env('YOUTUBE_LINK') }}" class="social-link">Youtube</a>
        <a href="{{ env('FACEBOOK_LINK') }}" class="social-link">Facebook</a>
        <a href="{{ env('INSTAGRAM_LINK') }}" class="social-link">Instagram</a>
        <a href="{{ env('TWITTER_LINK') }}" class="social-link">Twitter</a>
      </div>
      <div class="contact-info">@lang('footerMail.contact_us')</div>
      <div class="contact-info">@lang('footerMail.follow_us')</div>
      <div class="contact-info"><strong>@lang('footerMail.email')</strong> {{ env('MAIL_FROM_ADDRESS') }}</div>
      <div class="contact-info"><strong>@lang('footerMail.phone')</strong> {{ env('MAIL_FROM_PHONE') }}</div>
    </div>
    <hr>
  </div>
</body>

</html>
