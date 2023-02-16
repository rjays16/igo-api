<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SystemSetting;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;

class StoreUserRequest extends FormRequest
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
            "first_name" => "required|max:100",
            "last_name" => "required|max:100",
            "email" => "required|email|max:100",
            "address1" => "required|max:255",
            "address2" => "required|max:255",
            "city_id" => "required|numeric",
            "state" => "required|max:5",
            "zip" => "required|max:50",
            "phone" => "required|max:50",
            "role_id" => "required|numeric",
            "client_id" => "required|numeric",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'data'      => $validator->errors(),
            'message'   => SystemMessage::ValidationError,
            'success'   => false,
        ],
        HttpStatusCode::ClientErrorBadRequest));
    }
}
