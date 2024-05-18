<?php

namespace App\Enums;

enum PaymentMethodType: string
{
  case card = 'card';
    // case paypal = 'paypal';
  case imx = 'imx';
  // case google_pay = 'google_pay';
  // case apple_pay = 'apple_pay';
  case chargily = 'chargily';
}
