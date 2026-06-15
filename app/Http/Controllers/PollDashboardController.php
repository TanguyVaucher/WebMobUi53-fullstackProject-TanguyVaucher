<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Récupération des sondages de l'utilisateur pour alimenter le tableau de bord

class PollDashboardController extends Controller
{
    public function show(Request $request)
    {
        // Récupérer les polls d'un utilisateur, triés par date de création
        $polls = $request->user()->polls()->orderBy('created_at', 'desc')->get();

        // On affiche la vue, chargée avec les polls et le username utilisateur
        return view('polls.dashboard', [
            'polls'    => $polls,
            'username' => $request->user()->username,
        ]);
    }
}
