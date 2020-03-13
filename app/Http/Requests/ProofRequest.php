<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProofRequest extends FormRequest
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
            'id' => 'required|integer',
            'revision_id' => 'required|integer',
            'canvas_data' => 'required|JSON'
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }
}
