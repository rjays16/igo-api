<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;

class UpdateClientTypeRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "client_type" => "required|max:100",
            "description" => "required|max:100",
            //"tag" => "required",
            //"note" => "required",
            //"number" => "numeric:min:1|max:2",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
		    'data'      => $validator->errors(),
            'message'   => SystemMessage::ValidationError,
            'success'   => false
        ],
        HttpStatusCode::ClientTypeErrorBadRequest));
    }

    /*
    public function messages() //OPTIONAL
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email is not correct'
        ];
    }
    */
}
