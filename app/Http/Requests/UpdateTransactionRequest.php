<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;

class UpdateTransactionRequest extends FormRequest
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
            'account_id' => 'required|numeric',
            'effective_date' => 'required|date',
            'trans_type_id' => 'required|numeric',
            "memo" => "required|max:255",
            "amount" => 'required|numeric',
            'entry_date' => 'required|date',
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
