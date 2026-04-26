<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000'],
        ];
    }
}
