<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'committee_id' => 'required|exists:committees,id',
            'amount' => 'required|numeric',
            'paid_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'in:paid,pending'
        ];
    }
}