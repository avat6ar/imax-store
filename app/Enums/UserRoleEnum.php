<?php

namespace App\Enums;

enum UserRoleEnum: string
{
  case admin = 'admin';
  case super_admin = 'super_admin';
  case member = 'member';
}
