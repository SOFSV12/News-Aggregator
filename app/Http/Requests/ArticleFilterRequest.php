<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        return [
            'search' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'source' => ['sometimes', 'string', Rule::in(['nytimes', 'newsapi_ai', 'guardian', 'newsapi_org'])],
            'date_from' => 'sometimes|date|before_or_equal:date_to',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'sort' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'limit' => 'sometimes|integer|min:1|max:100',
            'author' => 'sometimes|string|max:100',
        ];
    }

     public function messages(): array
    {
        return [
            'source.in' => 'The selected source is invalid. Allowed sources are: nytimes, newsapi_ai, guardian, newsapi_org.',
            'date_from.before_or_equal' => 'The start date must be before or equal to the end date.',
            'date_to.after_or_equal' => 'The end date must be after or equal to the start date.',
            'sort.in' => 'The sort field must be either "asc" or "desc".',
            'limit.min' => 'The limit must be at least 1.',
            'limit.max' => 'The limit may not be greater than 100.',
        ];
    }
}
