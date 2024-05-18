<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
  <title>Imax Stroe - @lang('purchaseConfirmation.title')</title>
  <style>
    body {
      font-family: "Cairo", sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      color: #333;
    }

    .container {
      max-width: 540px;
      background: white;
      margin: auto;
      padding: 20px;
      text-align: center;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
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
      cursor: pointer;
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
      text-decoration: none;
      cursor: pointer;
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
    <h1 style="color: black; font-size: 24px; font-weight: bold;">@lang('purchaseConfirmation.greeting'), {{ $user->name }}!</h1>
    <p style="color: rgba(0, 0, 0, 0.60); font-size: 18px; font-weight: 400;">@lang('purchaseConfirmation.thank_you')</p>
    <p style="color: rgba(0, 0, 0, 0.60); font-size: 18px; font-weight: 400;">@lang('purchaseConfirmation.body')</p>

    <!-- رسالة تفاصيل الطلب -->
    <div style="margin-top: 20px; font-size: 18px; font-weight: 400;">
      <p>@lang('orderDetails.message') {{ $text }}</p>
      <p>@lang('orderDetails.order_name'): <strong
          style="color: #1577EB; font-size: 18px; font-weight: 700;">{{ $order->product->title_en }}</strong></p>
    </div>

    <p style="color: rgba(0, 0, 0, 0.60); font-size: 18px; font-weight: 400;">@lang('purchaseConfirmation.encouragement')</p>
    <a href="{{ env('FRONT_URL') . '/shop' }}" class="button" target="_black">@lang('footerMail.browse_products')</a>
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
    <hr>
  </div>
</body>

</html>
