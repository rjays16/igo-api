<?php

namespace App\Http\Requests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;

class UpdateRoleRequest extends FormRequest
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
            "role" => "required|max:150",
            "description" => "required|max:255",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
		    'data'      => $validator->errors(),
            'message'   => SystemMessage::ValidationError,
            'success'   => false   
        ],
        HttpStatusCode::RoleErrorBadRequest));
    }
}
