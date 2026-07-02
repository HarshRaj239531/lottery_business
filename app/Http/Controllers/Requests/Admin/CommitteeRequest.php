<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommitteeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'total_members' => 'required|integer',
            'duration' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'in:active,completed'
        ];
    }
}