<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Récupère les sondages de l'utilisateur connecté pour le tableau de bord

class PollDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $polls = $request->user()->polls()->orderBy('created_at', 'desc')->get();

        return view('polls.dashboard', [
            'polls'    => $polls,
            'username' => $request->user()->username,
        ]);
    }
}
