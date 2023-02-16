<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\SystemSetting;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;

class ViewRoleRequest extends FormRequest
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
            //For all sort parameters numeric 1 means ascending | 2 means descending
            "per_page" => "required|numeric|min:".SystemSetting::AllowedPageMinValue."|max:".SystemSetting::AllowedPageMaxValue."",
            "sort_role" => "numeric:min:1|max:2",
            "sort_description" => "numeric:min:1|max:2",
            "sort_permission" => "numeric:min:1|max:2",
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
