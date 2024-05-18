<?php

namespace App\Http\Requests;

use App\Enums\ProductInputTypeEnum;
use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProductRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'user_id' => $this->user()->id,
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title_en' => 'required|string|max:1000',
      'title_ar' => 'required|string|max:1000',
      'title_fr' => 'required|string|max:1000',
      'prices' => 'array|required',
      'prices.usd' => 'required',
      'prices.sar' => 'required',
      'prices.eur' => 'required',
      'prices.aed' => 'required',
      'prices.kwd' => 'required',
      'prices.egp' => 'required',
      'prices.dzd' => 'required',
      'prices.imx' => 'required',
      'status' => 'boolean',
      'type' => [
        'required',
        new Enum(ProductTypeEnum::class)
      ],
      'user_id' => 'required|exists:users,id',
      'category_id' => 'required|exists:product_categories,id',
      'description_en' => 'required|string',
      'description_ar' => 'required|string',
      'description_fr' => 'required|string',
      'questions' => 'array',
      'questions.*.question_en' => 'required|string',
      'questions.*.question_ar' => 'required|string',
      'questions.*.question_fr' => 'required|string',
      'questions.*.answer_en' => 'string',
      'questions.*.answer_ar' => 'string',
      'questions.*.answer_fr' => 'string',
      'codes' => 'array',
      'codes.*.code' => 'required|string|max:255',
      'codes.*.expire' => 'boolean',
      'codes.*.expire_date' => 'required|date',
      'inputs' => 'array',
      'inputs.*.title_ar' => 'required|string',
      'inputs.*.title_fr' => 'required|string',
      'inputs.*.title_en' => 'required|string',
      'inputs.*.type' => [
        'required',
        new Enum(ProductInputTypeEnum::class)
      ],
      'inputs.*.data' => 'present',
      'seo' => 'required|array',
      'seo.title_en' => 'required|string',
      'seo.title_ar' => 'required|string',
      'seo.title_fr' => 'required|string',
      'seo.description_en' => 'required|string',
      'seo.description_ar' => 'required|string',
      'seo.description_fr' => 'required|string',
      'seo.keywords_en' => 'required|string',
      'seo.keywords_ar' => 'required|string',
      'seo.keywords_fr' => 'required|string',
      'images' => 'required|array',
      'images.*.id' => 'integer',
      'images.*.image' => 'required|string',
    ];
  }
}
