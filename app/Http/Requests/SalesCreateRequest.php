<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SalesCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'tax' => 'required|integer|min:0|max:100',
            'final_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:unpaid,paid',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card',
            'branch_id' => 'required|exists:branches,id',
            'unit_price' => 'required|numeric|min:0',
            'sales_items' => 'required|array',
            'sales_items.*.product_id' => 'required|exists:products,id',
            'sales_items.*.quantity' => 'required|integer|min:1',
            'sales_items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400)
        );
    }
}
