<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailNotificationSettingsRequest extends FormRequest
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
            'new_project'       => 'boolean',
            'new_comment'       => 'boolean',
            'new_correction'    => 'boolean',
            'new_revision'      => 'boolean',
            'approved_project'  => 'boolean',
            'completed_project' => 'boolean',
        ];
    }

    public function messages()
    {
        return [];
    }
}