<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
class PermissionRequest extends FormRequest
{
    protected $rules = [];
    protected $messages = [];

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
        $this->rules['module_id'] = ['required','integer'];
        $this->messages['module_id.required']  = 'The module field is required';
        $this->messages['module_id.integer']  = 'The module field value must be integer';

        $collection = collect(request());
        if($collection->has('permission')){
            foreach (request()->permission as $key => $value) {
                $this->rules['permission.'.$key.'.name'] = ['required','string'];
                $this->rules['permission.'.$key.'.slug'] = ['required','string','unique:permissions,slug'];

                $this->messages['permission.'.$key.'.name.required'] = 'The name field is required';
                $this->messages['permission.'.$key.'.name.string'] = 'The name field value must be string';
                $this->messages['permission.'.$key.'.slug.required'] = 'The slug field is required';
                $this->messages['permission.'.$key.'.slug.string'] = 'The slug field value must be string';
                $this->messages['permission.'.$key.'.slug.unique'] = 'The slug field has already been taken try another one';
            }
        }

        return $this->rules;
    }

    public function messages()
    {
        return $this->messages;
    }
}
