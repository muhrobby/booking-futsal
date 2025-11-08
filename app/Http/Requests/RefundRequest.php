<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin can process refunds
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $order = $this->route('order');
        
        return [
            'reason' => 'required|string|max:500',
            'amount' => 'nullable|numeric|min:0|max:' . ($order ? $order->total : 0),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan refund harus diisi',
            'reason.max' => 'Alasan refund maksimal 500 karakter',
            'amount.numeric' => 'Jumlah refund harus berupa angka',
            'amount.min' => 'Jumlah refund minimal 0',
            'amount.max' => 'Jumlah refund tidak boleh melebihi total order',
        ];
    }
}
