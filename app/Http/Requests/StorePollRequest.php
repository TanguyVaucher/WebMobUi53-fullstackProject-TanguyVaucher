<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Sous-classe de FormRequest de Laravel pour la validation des polls

class StorePollRequest extends FormRequest
{

    // La connexion est vérifiée côté middleware de la route
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validation des champs / options
            'question'               => ['required', 'string', 'max:255'],
            'options'                => ['required', 'array', 'min:2'],
            'options.*.label'        => ['required', 'string', 'max:255'], // Une option doit avoir un label
            'allow_multiple_choices' => ['boolean'],
            'allow_vote_change'      => ['boolean'],
            'results_public'         => ['boolean'],
            // Durée de validité (en secondes, minimum 60)
            'duration'               => ['nullable', 'integer', 'min:60'],
            // Thèmes de couleur
            'color'                  => ['nullable', 'string', 'in:indigo,violet,sky,teal,pink,orange'],
            // Brouillon (peut être activé à la création)
            'is_draft'               => ['boolean'],
        ];
    }
}
