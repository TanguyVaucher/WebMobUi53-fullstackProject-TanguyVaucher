<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePollVoteRequest extends FormRequest
{
    // La connexion est vérifiée côté middleware de la route
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Le vote doit contenir au moins une option
            'option_ids'   => ['required', 'array', 'min:1'],
            // Chaque option envoyee doit etre unique et exister en bdd
            'option_ids.*' => ['integer', 'distinct', 'exists:poll_options,id'],
        ];
    }
}
