### 2. Remplacez `resources/views/filament/membres/statistiques-ages-pdf.blade.php`

Ce modèle PDF génère **en paysage (A4 Landscape)** les statistiques globales en haut, les totaux par Conseil/Section, puis la **liste détaillée complète de toutes les personnes** :

```blade
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste & Statistiques par Âge - D.M.W.W / COMITÉ TAMBA</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #0f172a;
        }

        h1 {
            font-size: 14px;
            color: #0369a1;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        h2 {
            font-size: 11px;
            color: #1e293b;
            margin: 14px 0 6px 0;
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 3px;
        }

        .meta {
            font-size: 9px;
            color: #475569;
            margin: 0 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
        }

        th {
            background-color: #e2e8f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }

        .center {
            text-align: center;
        }

        .total-row {
            background-color: #f1f5f9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>D.M.W.W / COMITÉ TAMBA — Effectifs & Liste Détaillée par Tranche d'Âge</h1>
    <p class="meta">Généré le {{ now()->format('d/m/Y à H:i') }}</p>

    {{-- Statistiques globales en haut --}}
    <table>
        <thead>
            <tr>
                <th>Enfants (0 - 17 ans)</th>
                <th>Jeunes (18 - 35 ans)</th>
                <th>Adultes (36 ans et +)</th>
                <th>Effectif Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="center">
                <td><strong>{{ $totaux['enfants'] }}</strong></td>
                <td><strong>{{ $totaux['jeunes'] }}</strong></td>
                <td><strong>{{ $totaux['adultes'] }}</strong></td>
                <td><strong>{{ $totaux['total'] }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h2>Répartition par Conseil et Section</h2>
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Conseil</th>
                <th style="text-align: left;">Section</th>
                <th class="center">Enfants (0-17)</th>
                <th class="center">Jeunes (18-35)</th>
                <th class="center">Adultes (36+)</th>
                <th class="center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parSection as $s)
            <tr>
                <td>{{ $s->Conseil ?: 'Non défini' }}</td>
                <td>{{ $s->Section ?: 'Non définie' }}</td>
                <td class="center">{{ $s->enfants }}</td>
                <td class="center">{{ $s->jeunes }}</td>
                <td class="center">{{ $s->adultes }}</td>
                <td class="center"><strong>{{ $s->total }}</strong></td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL GÉNÉRAL</td>
                <td class="center">{{ $totaux['enfants'] }}</td>
                <td class="center">{{ $totaux['jeunes'] }}</td>
                <td class="center">{{ $totaux['adultes'] }}</td>
                <td class="center">{{ $totaux['total'] }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Liste Complète des Membres Classés par Conseil, Section et Catégorie d'Âge</h2>
    <table>
        <thead>
            <tr>
                <th>Catégorie</th>
                <th>Conseil</th>
                <th>Section</th>
                <th>Matricule</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th class="center">Âge</th>
                <th>Genre</th>
                <th>Téléphone</th>
                <th>Profession</th>
                <th>Niveau Étude</th>
                <th>Adresse</th>
                <th>Pièce ID</th>
                <th>Parent / Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($membres as $m)
            <tr>
                <td><strong>{{ $m->categorie_age }}</strong></td>
                <td>{{ $m->Conseil }}</td>
                <td>{{ $m->Section }}</td>
                <td>{{ $m->internal_id }}</td>
                <td>{{ $m->Prenom }}</td>
                <td><strong>{{ $m->Nom }}</strong></td>
                <td class="center">{{ $m->Age }}</td>
                <td>{{ $m->Genre }}</td>
                <td>{{ $m->Telephone }}</td>
                <td>{{ $m->Profession }}</td>
                <td>{{ $m->Niveau_Etude }}</td>
                <td>{{ $m->Adresse }}</td>
                <td>{{ trim($m->Type_piece_identite . ' ' . $m->Numero_piece_identite) }}</td>
                <td>{{ $m->Parent ? $m->Parent . ' (' . $m->Contact . ')' : $m->Contact }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>