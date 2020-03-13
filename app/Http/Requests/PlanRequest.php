<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
            'name'              => 'required|string',
            'description'       => 'required|string|max:255',
            'price'             => 'required|integer|min:1',
            'teams_count'        => 'required|integer|min:0',
            'teams_members_count' => 'required|integer|min:0'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
