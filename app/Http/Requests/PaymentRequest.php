<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\PaymentCurrencyType;
use App\Enums\PaymentMethodType;

class PaymentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'currency' => [
        'required',
        new Enum(PaymentCurrencyType::class)
      ],
      'method' => [
        'required',
        new Enum(PaymentMethodType::class)
      ],
    ];
  }
}
