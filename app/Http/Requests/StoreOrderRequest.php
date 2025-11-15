<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true; // no auth required per instructions
    }


    public function rules()
    {
        return [
            'customer_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
        ];
    }
}
