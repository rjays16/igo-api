<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;

class UpdateAccountRequest extends FormRequest
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
            "status_id" => "required|numeric",
            "status" => "required",
            "acct_description" => "required|max:255",
            "debtor_id" => "required|numeric",
            "note" => "required|max:255",
            "origin_date" => "required|date",
            "tag" => "required|max:255"
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
