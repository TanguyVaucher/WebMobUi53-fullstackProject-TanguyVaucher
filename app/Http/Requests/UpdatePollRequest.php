<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePollRequest extends FormRequest
{
    // Propriétaire vérifié dans le contrôleur
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // "sometimes" = validé uniquement si le champ est envoyé
            'question'               => ['sometimes', 'string', 'max:255'],
            'options'                => ['sometimes', 'array', 'min:2'],
            'options.*.id'           => ['sometimes', 'integer', 'exists:poll_options,id'],
            'options.*.label'        => ['required_with:options', 'string', 'max:255'],
            'allow_multiple_choices' => ['boolean'],
            'allow_vote_change'      => ['boolean'],
            'results_public'         => ['boolean'],
            'duration'               => ['nullable', 'integer', 'min:60'],
            'color'                  => ['nullable', 'string', 'in:indigo,violet,sky,teal,pink,orange'],
            'is_draft'               => ['boolean'],
        ];
    }
}
