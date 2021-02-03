<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRegisterRequest extends FormRequest
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
            'number' => 'numeric|nullable',
            'name' => 'required|string',
            'pass' => 'required',
            'expense' => 'integer|nullable|min:0',
            'salary' => 'required|integer|min:0',
        ];
    }

    public function messages(){
        return [
            'number.numeric' => '整数値で入力してください',
            'name.required' => '名前の設定は必須です',
            'name.string' => '文字列で５文字以内にしてください',
            'pass.required' => 'パスワードの設定は必須です',
            'expense.integer' => '整数値で入力してください',
            'expense.min' => '値は0以上にする必要があります',
            'salary.required' => '時給の設定は必須です',
            'salary.integer' => '整数値で入力してください',
            'salary.min' => '値は0以上にする必要があります',
        ];
    }
}
