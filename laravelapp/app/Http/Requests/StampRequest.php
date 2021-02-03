<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StampRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'expense' => 'integer|nullable|min:0',
        ];
    }

    public function messages()
    {
        return [
            'expense.integer' => '整数値で入力してください',
            'expense.min' => '値は0以上にする必要があります',
        ];
    }

}
