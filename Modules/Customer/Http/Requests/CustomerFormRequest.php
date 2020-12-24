<?php

namespace Modules\Customer\Http\Requests;

use App\Http\Requests\FormRequest;

class CustomerFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['customer_group_id']  = ['required','integer'];
        $rules['name']         = ['required','string'];
        $rules['company_name'] = ['nullable','string'];
        $rules['tax_number']   = ['nullable','string'];
        $rules['phone']        = ['nullable','string','unique:suppliers,phone'];
        $rules['email']        = ['nullable','email','string','unique:suppliers,email'];
        $rules['address']      = ['nullable','string'];
        $rules['city']         = ['nullable','string'];
        $rules['state']        = ['nullable','string'];
        $rules['postal_code']  = ['nullable','string'];
        $rules['country']      = ['nullable','string'];

        if(request()->update_id){
            $rules['phone'][2] = 'unique:suppliers,phone,'.request()->update_id;
            $rules['email'][3] = 'unique:suppliers,email,'.request()->update_id;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'customer_group_id.required' => 'The customer group field is required',
            'customer_group_id.integer' => 'The customer group field value must be integer'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
