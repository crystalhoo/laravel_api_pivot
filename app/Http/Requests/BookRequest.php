<?php

namespace App\Http\Requests;

use App\Rules\IsbnRule;

class BookRequest extends ApiFormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'isbn'          => ['required', 'string', new IsbnRule],
            'title'         => 'required|string|max:150',
            'year'          => 'required|integer',
            'publisher_id'  => 'required|integer',
            'authors'       => 'required|array',
            'authors.*'     => 'integer',
        ];
    }
}
