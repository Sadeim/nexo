<?php

namespace App\Http\Requests\StaticPage;

use App\Http\Requests\BaseRequest;

class CreateStaticPageRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
        return [
            'title'      => 'required|string|max:255',
            'content'    => 'required',
            'slug'          => 'required|string|max:255|unique:static_pages,slug',
        ];  
    }
}
