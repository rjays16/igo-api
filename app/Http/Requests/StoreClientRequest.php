<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;



class StoreClientRequest extends FormRequest
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
            "gender" => "required|max:6",
            "date_of_birth" => "required|date",
            "email" => "required|email|max:100",
            "phone" => "required|max:50",
            "organization_id" => "required|numeric",
            "address1" => "required|max:255",
            "city_id" => "required|numeric",
            "state" => "required|max:5",
            "zip" => "required|max:50",
            "client_type_id" => "required|numeric",
            "ca_date" => "required|date",
            "tag" => "max:255",
            "note" => "max:255",
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
