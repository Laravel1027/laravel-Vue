<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileRequest extends FormRequest
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
            'photos' => 'required|file|image|dimensions:min_width=640,min_height=480',
            'owner_type' => 'required|alpha|in:proof,issue,comment',
            'file_type' => 'required|alpha|in:picture,pdf',
            'photos' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'photos.dimensions' => 'The uploaded image file is to small. The minimun acepted size is 640x480 pixels.',
            'photos.image' => 'Invalid image format. Acepted format are (jpeg, png, svg, bmp or gif)',
        ];
    }
}
