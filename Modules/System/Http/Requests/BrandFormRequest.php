<?php

namespace Modules\System\Http\Requests;

use App\Http\Requests\FormRequest;


class BrandFormRequest extends FormRequest
{
    protected $rules = [];
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->rules['title'] = ['required','string','unique:brands,title'];
        $this->rules['image'] = ['required','image','mimes:png,jpg,jpeg'];
        if(request()->update_id){
            $this->rules['title'][2] = 'unique:brands,title,'.request()->update_id;
            $this->rules['image'][0] = 'nullable';
        }
        return $this->rules;
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
