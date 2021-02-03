<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRegisterRequest extends FormRequest
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
            'go_time' => 'required',
            'out_time' => 'required',
            'expense' => 'integer|required|min:0',
        ];
    }

    public function messages()
    {
        return [
            'go_time.required' => '空欄は認められません',
            'out_time.required' => '空欄は認められません',
            'expense.integer' => '整数値で入力してください',
            'expense.required' => '空欄は認められません',
            'expense.min' => '値は0以上にする必要があります',
        ];
    }
}
