<?php

namespace App\Http\Controllers\Api\Medi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediBotController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = mb_strtolower($validated['message']);

        $answer = match (true) {
            str_contains($message, 'patient') && str_contains($message, 'connect') =>
                'Un patient se connecte avec son CIN et sa date de naissance. Après connexion, il voit son médecin et ses traitements sous forme de texte.',
            str_contains($message, 'médecin') && str_contains($message, 'connect') =>
                'Le médecin se connecte avec le code confidentiel envoyé après son inscription. Ce code donne accès à son espace de travail.',
            str_contains($message, 'medecin') && str_contains($message, 'connect') =>
                'Le médecin se connecte avec le code confidentiel envoyé après son inscription. Ce code donne accès à son espace de travail.',
            str_contains($message, 'ordonnance') || str_contains($message, 'traitement') =>
                "Le médecin peut uploader une ordonnance ou saisir un traitement. MediRecord propose une extraction texte, puis le médecin corrige et valide avant l'enregistrement.",
            str_contains($message, 'historique') =>
                "L'historique patient affiche les ordonnances déjà validées, sous forme de texte, avec le médecin et la date de validation.",
            str_contains($message, 'secur') || str_contains($message, 'confidential') =>
                "Les documents scannés ne sont pas affichés aux autres médecins dans l'historique. Les informations utiles sont conservées sous forme de texte validé.",
            default =>
                'Je suis MediBot. Je peux vous expliquer comment utiliser MediRecord: inscription patient, connexion médecin, upload ordonnance, validation IA, historique patient et confidentialité. Je ne remplace pas un avis médical.',
        };

        return response()->json([
            'data' => [
                'answer' => $answer,
                'disclaimer' => 'MediBot aide à utiliser MediRecord, mais ne remplace pas un médecin.',
            ],
        ]);
    }
}
