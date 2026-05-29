<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordonnance #{{ $ordonnance->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; margin: 0; }
        .header { border-bottom: 3px solid #0d7a5f; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { color: #0d7a5f; margin: 0 0 4px 0; font-size: 22px; }
        .header .small { color: #555; font-size: 11px; }
        .section { margin-bottom: 16px; }
        .section h2 { font-size: 13px; background:#0d7a5f; color:#fff; padding:6px 10px; margin:0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 4px 8px; vertical-align: top; }
        .grid { display: table; width: 100%; }
        .grid > div { display: table-cell; width: 50%; vertical-align: top; padding-right: 14px; }
        .label { color:#666; font-weight: bold; display:inline-block; min-width: 110px; }
        .med-table th { background: #f0f0f0; text-align: left; padding: 6px 8px; border:1px solid #ddd; font-size:11px; }
        .med-table td { border: 1px solid #ddd; padding: 6px 8px; }
        .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 11px; color:#666; }
        .signature { margin-top: 40px; text-align: right; }
        .signature .name { font-weight: bold; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; text-transform: uppercase; }
        .status-active { background: #d4f4dd; color: #0d7a5f; }
        .status-expiree { background: #fff3cd; color: #856404; }
        .status-annulee { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ORDONNANCE MÉDICALE</h1>
        <div class="small">
            N° {{ str_pad($ordonnance->id, 6, '0', STR_PAD_LEFT) }} &middot;
            Émise le {{ $ordonnance->date_emission->format('d/m/Y') }}
            @if($ordonnance->date_expiration)
                &middot; Valable jusqu'au {{ $ordonnance->date_expiration->format('d/m/Y') }}
            @endif
            &middot; <span class="status status-{{ $ordonnance->statut }}">{{ $ordonnance->statut }}</span>
        </div>
    </div>

    <div class="grid section">
        <div>
            <h2>Médecin prescripteur</h2>
            <p>
                <span class="label">Nom :</span> Dr. {{ $ordonnance->medecin->user->name ?? '—' }}<br>
                <span class="label">Spécialité :</span> {{ $ordonnance->medecin->specialite ?? '—' }}<br>
                <span class="label">N° ordre :</span> {{ $ordonnance->medecin->numero_ordre ?? '—' }}<br>
                <span class="label">Hôpital :</span> {{ $ordonnance->medecin->hopital ?? '—' }}<br>
                <span class="label">Email :</span> {{ $ordonnance->medecin->user->email ?? '—' }}
            </p>
        </div>
        <div>
            <h2>Patient</h2>
            <p>
                <span class="label">Nom :</span> {{ $ordonnance->patient->user->name ?? '—' }}<br>
                <span class="label">Date naissance :</span>
                {{ optional($ordonnance->patient->user->profile?->date_naissance)->format('d/m/Y') ?? '—' }}<br>
                <span class="label">Genre :</span> {{ $ordonnance->patient->user->profile?->genre ?? '—' }}<br>
                <span class="label">Groupe sanguin :</span> {{ $ordonnance->patient->groupe_sanguin ?? '—' }}<br>
                <span class="label">Téléphone :</span> {{ $ordonnance->patient->user->phone ?? '—' }}<br>
                <span class="label">Adresse :</span>
                {{ trim(($ordonnance->patient->user->profile?->adresse ?? '').' '.($ordonnance->patient->user->profile?->ville ?? '')) ?: '—' }}
            </p>
        </div>
    </div>

    @if($ordonnance->patient->allergies || $ordonnance->patient->antecedents)
    <div class="section">
        <h2>Données du dossier patient</h2>
        @if($ordonnance->patient->allergies)
            <p><span class="label">Allergies :</span> {{ $ordonnance->patient->allergies }}</p>
        @endif
        @if($ordonnance->patient->antecedents)
            <p><span class="label">Antécédents :</span> {{ $ordonnance->patient->antecedents }}</p>
        @endif
    </div>
    @endif

    @if($ordonnance->consultation)
    <div class="section">
        <h2>Consultation associée</h2>
        <p>
            <span class="label">Date :</span> {{ $ordonnance->consultation->date_heure->format('d/m/Y H:i') }}<br>
            <span class="label">Motif :</span> {{ $ordonnance->consultation->motif }}<br>
            @if($ordonnance->consultation->diagnostic)
                <span class="label">Diagnostic :</span> {{ $ordonnance->consultation->diagnostic }}
            @endif
        </p>
    </div>
    @endif

    <div class="section">
        <h2>Prescription</h2>
        <table class="med-table">
            <thead>
                <tr>
                    <th style="width:25%">Médicament</th>
                    <th style="width:12%">Forme</th>
                    <th style="width:13%">Dosage</th>
                    <th style="width:18%">Fréquence</th>
                    <th style="width:12%">Durée</th>
                    <th style="width:20%">Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordonnance->medicaments as $m)
                <tr>
                    <td><strong>{{ $m->nom }}</strong></td>
                    <td>{{ $m->forme }}</td>
                    <td>{{ $m->pivot->dosage }}</td>
                    <td>{{ $m->pivot->frequence }}</td>
                    <td>{{ $m->pivot->duree }}</td>
                    <td>{{ $m->pivot->instructions ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($ordonnance->instructions_generales)
    <div class="section">
        <h2>Instructions générales</h2>
        <p>{{ $ordonnance->instructions_generales }}</p>
    </div>
    @endif

    <div class="signature">
        <div class="name">Dr. {{ $ordonnance->medecin->user->name ?? '' }}</div>
        <div>{{ $ordonnance->medecin->specialite ?? '' }}</div>
        <div>Signature & cachet</div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} par MediRecord.
        Ce document a valeur d'ordonnance médicale. À conserver.
    </div>
</body>
</html>
