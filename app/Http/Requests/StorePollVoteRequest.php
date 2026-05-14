<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePollVoteRequest extends FormRequest
{
    // Authentification vérifiée par le middleware de la route
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'option_ids'   => ['required', 'array', 'min:1'],
            // chaque id doit exister dans la table poll_options
            'option_ids.*' => ['integer', 'distinct', 'exists:poll_options,id'],
        ];
    }
}
