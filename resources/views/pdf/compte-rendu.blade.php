<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte-rendu d'intervention n° CR-{{ $cr->id }}</title>
    <style>
        @page { margin: 8mm 10mm; size: A4 portrait; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        /* En-tête */
        .header {
            border-bottom: 1.5px solid #1e3a5f;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 15px;
            color: #1e3a5f;
            text-transform: uppercase;
        }
        .header .subtitle {
            margin: 2px 0 0;
            font-size: 9px;
            color: #555;
        }

        /* Infos en 2 colonnes */
        .info-row {
            width: 100%;
            margin-bottom: 4px;
        }
        .info-col {
            display: inline-block;
            width: 49%;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #1e3a5f;
            font-size: 8.5px;
        }
        .info-value {
            font-size: 10px;
        }

        /* Sections */
        .section-title {
            background: #1e3a5f;
            color: #fff;
            padding: 3px 6px;
            margin: 8px 0 0;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
        }
        .content-box {
            border: 0.5px solid #bbb;
            border-top: none;
            padding: 5px 6px;
            background: #fdfdfd;
        }
        .content-box p {
            margin: 0;
            font-size: 10px;
        }
        .content-box .empty {
            color: #888;
            font-style: italic;
        }

        /* Coûts */
        .costs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 10px;
        }
        .costs-table th, .costs-table td {
            border: 0.5px solid #bbb;
            padding: 4px 6px;
        }
        .costs-table th {
            background: #eef2f7;
            color: #1e3a5f;
            font-weight: bold;
            font-size: 9px;
        }
        .costs-table .total-row {
            font-weight: bold;
            background: #eef2f7;
        }
        .costs-table .total-row td {
            border-top: 1.5px solid #1e3a5f;
        }

        /* Statut */
        .status-badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }
        .status-valide { background: #d4edda; color: #155724; }
        .status-soumis { background: #cce5ff; color: #004085; }
        .status-refuse { background: #f8d7da; color: #721c24; }
        .status-brouillon { background: #e2e3e5; color: #383d41; }

        /* Signatures */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 6px;
            border: 0.5px solid #bbb;
        }
        .signatures-table .sig-title {
            font-weight: bold;
            color: #1e3a5f;
            font-size: 9px;
            margin-bottom: 50px;
        }
        .signatures-table .sig-line {
            border-top: 0.5px solid #333;
            padding-top: 2px;
            font-size: 9px;
        }
        .signatures-table .sig-date {
            font-size: 7.5px;
            color: #666;
            margin-top: 1px;
        }

        /* Commentaire */
        .comment-box {
            border: 0.5px solid #bbb;
            border-top: none;
            padding: 5px 6px;
            background: #fdfdfd;
        }

        /* Footer */
        .footer {
            margin-top: 6px;
            font-size: 7.5px;
            color: #888;
            text-align: center;
            border-top: 0.5px solid #ddd;
            padding-top: 3px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Compte-rendu d'intervention</h1>
    <div class="subtitle">N° CR-{{ $cr->id }} — Établi le {{ $cr->date_soumission?->format('d/m/Y H:i') ?? '—' }}</div>
</div>

<!-- INFOS EN 2 COLONNES -->
<div class="info-row">
    <div class="info-col">
        <span class="info-label">Titre :</span> <span class="info-value">{{ $intervention?->titre ?? '—' }}</span><br>
        <span class="info-label">Service :</span> <span class="info-value">{{ $service?->nom ?? '—' }}</span><br>
        <span class="info-label">Équipement :</span> <span class="info-value">{{ $equipement?->nom ?? '—' }} ({{ $equipement?->numero_serie ?? '—' }})</span>
    </div>
    <div class="info-col">
        <span class="info-label">Technicien :</span> <span class="info-value">{{ $technicien?->name ?? '—' }}</span><br>
        <span class="info-label">Statut :</span>
        @php
            $statusClass = match($cr->statut) {
                \App\Enums\StatutCompteRendu::Valide => 'status-valide',
                \App\Enums\StatutCompteRendu::Soumis => 'status-soumis',
                \App\Enums\StatutCompteRendu::Refuse => 'status-refuse',
                default => 'status-brouillon',
            };
        @endphp
        <span class="status-badge {{ $statusClass }}">{{ $cr->statut->getLabel() }}</span>
    </div>
</div>

<!-- RAPPORT TECHNIQUE -->
<div class="section-title">Rapport technique</div>
<div class="content-box">
    <p><strong style="font-size: 9px; color: #555;">Observations / travaux réalisés :</strong></p>
    <p class="{{ $cr->observations ? '' : 'empty' }}">{{ $cr->observations ?? 'Aucune observation' }}</p>
</div>
<div class="content-box" style="border-top: 1px dashed #ddd;">
    <p><strong style="font-size: 9px; color: #555;">Pièces et consommables utilisés :</strong></p>
    <p class="{{ $cr->pieces_utilisees ? '' : 'empty' }}">{{ $cr->pieces_utilisees ?? 'Aucune pièce' }}</p>
</div>

<!-- DETAIL DES COUTS -->
<div class="section-title">Détail des coûts</div>
<table class="costs-table">
    <tr>
        <th style="width: 60%;">Poste</th>
        <th style="width: 40%; text-align: right;">Montant</th>
    </tr>
    <tr>
        <td>Temps passé</td>
        <td style="text-align: right;">{{ $cr->temps_passe ?? '0' }} h</td>
    </tr>
    <tr>
        <td>Main d'œuvre</td>
        <td style="text-align: right;">{{ number_format($cr->cout_main_oeuvre ?? 0, 2, ',', ' ') }} MAD</td>
    </tr>
    <tr>
        <td>Pièces / consommables</td>
        <td style="text-align: right;">{{ number_format($cr->cout_pieces ?? 0, 2, ',', ' ') }} MAD</td>
    </tr>
    <tr class="total-row">
        <td>COÛT TOTAL</td>
        <td style="text-align: right;">{{ number_format($cr->cout_total ?? 0, 2, ',', ' ') }} MAD</td>
    </tr>
</table>

<!-- SIGNATURES -->
<div class="section-title">Signatures</div>
<table class="signatures-table">
    <tr>
        <td>
            <div class="sig-title">Technicien intervenant</div>
            <div class="sig-line">{{ $cr->signature_technicien ?? $technicien?->name ?? '—' }}</div>
            <div class="sig-date">{{ $cr->date_soumission?->format('d/m/Y H:i') ?? '—' }}</div>
        </td>
        <td>
            <div class="sig-title">Chef de service</div>
            <div class="sig-line">{{ $cr->signature_chef_service ?? 'En attente de validation' }}</div>
            <div class="sig-date">{{ $cr->date_validation?->format('d/m/Y H:i') ?? '—' }}</div>
        </td>
    </tr>
</table>

@if($cr->commentaire_validation)
<div class="section-title">Commentaire de validation</div>
<div class="comment-box">
    <p>{{ $cr->commentaire_validation }}</p>
</div>
@endif

<!-- FOOTER -->
<div class="footer">
    Document généré le {{ now()->format('d/m/Y H:i') }} — GMAO Clinique
</div>

</body>
</html>
