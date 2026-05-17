<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePollRequest extends FormRequest
{
    // Tout utilisateur authentifié peut créer un sondage
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question'               => ['required', 'string', 'max:255'],
            'options'                => ['required', 'array', 'min:2'],
            'options.*.label'        => ['required', 'string', 'max:255'],
            'allow_multiple_choices' => ['boolean'],
            'allow_vote_change'      => ['boolean'],
            'results_public'         => ['boolean'],
            // duration en secondes, min 1 minute
            'duration'               => ['nullable', 'integer', 'min:60'],
            // thème couleur
            'color'                  => ['nullable', 'string', 'in:indigo,violet,sky,teal,pink,orange'],
            // on peut lancer le sondage directement à la création
            'is_draft'               => ['boolean'],
        ];
    }
}
