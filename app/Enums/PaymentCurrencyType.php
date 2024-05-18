<?php

namespace App\Enums;

enum PaymentCurrencyType: string
{
  case usd = 'usd';
  case eur = 'eur';
  case aed = 'aed';
  case sar = 'sar';
  case kwd = 'kwd';
  case dzd = 'dzd';
  case egp = 'egp';
  case imx = 'imx';
}
