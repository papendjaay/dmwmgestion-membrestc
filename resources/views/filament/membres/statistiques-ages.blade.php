<div x-data="{ filtre: 'tous', recherche: '' }" style="font-family: Inter, sans-serif;">
    <style>
        .age-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .age-stat-card {
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .dark .age-stat-card {
            background: #1e293b;
            border-color: #334155;
        }

        .age-stat-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #475569;
        }

        .dark .age-stat-title {
            color: #94a3b8;
        }

        .age-stat-val {
            font-size: 24px;
            font-weight: 800;
            margin: 2px 0;
            color: #0f172a;
        }

        .dark .age-stat-val {
            color: #f8fafc;
        }

        .age-stat-sub {
            font-size: 12px;
            color: #64748b;
        }

        .age-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #cbd5e1;
        }

        .dark .age-toolbar {
            border-color: #334155;
        }

        .age-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .age-tab-btn {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            background: #ffffff;
            color: #1e293b;
            transition: 0.15s;
        }

        .dark .age-tab-btn {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }

        .age-tab-btn.active {
            background: #0369a1 !important;
            color: #ffffff !important;
            border-color: #0369a1 !important;
        }

        .age-search {
            padding: 7px 12px;
            border-radius: 8px;
            border: 1px solid #94a3b8;
            font-size: 13px;
            min-width: 260px;
            background: #ffffff;
            color: #0f172a;
        }

        .dark .age-search {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        .age-table-wrap {
            overflow-x: auto;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            max-height: calc(100vh - 290px);
        }

        .dark .age-table-wrap {
            border-color: #334155;
        }

        .age-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            text-align: left;
        }

        .age-table th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #e2e8f0;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            padding: 10px 12px;
            border-bottom: 1px solid #94a3b8;
            white-space: nowrap;
        }

        .dark .age-table th {
            background: #0f172a;
            color: #f8fafc;
            border-color: #334155;
        }

        .age-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
            white-space: nowrap;
        }

        .dark .age-table td {
            border-color: #1e293b;
            color: #e2e8f0;
        }

        .age-table tr:hover td {
            background: #f1f5f9;
        }

        .dark .age-table tr:hover td {
            background: #1e293b;
        }

        .cat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
        }

        .cat-enfant {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #7dd3fc;
        }

        .cat-jeune {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .cat-adulte {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
    </style>

    {{-- 1. STATISTIQUES GLOBALES EN HAUT --}}
    <div class="age-stats-grid">
        <div class="age-stat-card" style="border-left: 4px solid #0284c7;">
            <div class="age-stat-title">Enfants (0 - 17 ans)</div>
            <div class="age-stat-val">{{ $totaux['enfants'] }}</div>
            <div class="age-stat-sub">{{ $totaux['total'] > 0 ? round(($totaux['enfants'] / $totaux['total']) * 100, 1) : 0 }} % de l'effectif</div>
        </div>

        <div class="age-stat-card" style="border-left: 4px solid #16a34a;">
            <div class="age-stat-title">Jeunes (18 - 35 ans)</div>
            <div class="age-stat-val">{{ $totaux['jeunes'] }}</div>
            <div class="age-stat-sub">{{ $totaux['total'] > 0 ? round(($totaux['jeunes'] / $totaux['total']) * 100, 1) : 0 }} % de l'effectif</div>
        </div>

        <div class="age-stat-card" style="border-left: 4px solid #d97706;">
            <div class="age-stat-title">Adultes (36 ans et +)</div>
            <div class="age-stat-val">{{ $totaux['adultes'] }}</div>
            <div class="age-stat-sub">{{ $totaux['total'] > 0 ? round(($totaux['adultes'] / $totaux['total']) * 100, 1) : 0 }} % de l'effectif</div>
        </div>

        <div class="age-stat-card" style="border-left: 4px solid #475569;">
            <div class="age-stat-title">Effectif Total</div>
            <div class="age-stat-val">{{ $totaux['total'] }}</div>
            <div class="age-stat-sub">Tous membres confondus</div>
        </div>
    </div>

    {{-- 2. BARRE D'ONGLETS ET DE RECHERCHE --}}
    <div class="age-toolbar">
        <div class="age-tabs">
            <button type="button" @click="filtre = 'tous'" :class="{ 'active': filtre === 'tous' }" class="age-tab-btn">
                Tous ({{ $totaux['total'] }})
            </button>
            <button type="button" @click="filtre = 'ENFANT'" :class="{ 'active': filtre === 'ENFANT' }" class="age-tab-btn">
                Enfants ({{ $totaux['enfants'] }})
            </button>
            <button type="button" @click="filtre = 'JEUNE'" :class="{ 'active': filtre === 'JEUNE' }" class="age-tab-btn">
                Jeunes ({{ $totaux['jeunes'] }})
            </button>
            <button type="button" @click="filtre = 'ADULTE'" :class="{ 'active': filtre === 'ADULTE' }" class="age-tab-btn">
                Adultes ({{ $totaux['adultes'] }})
            </button>
            <button type="button" @click="filtre = 'synthese'" :class="{ 'active': filtre === 'synthese' }" class="age-tab-btn">
                Totaux par Conseil & Section
            </button>
        </div>

        <input x-show="filtre !== 'synthese'"
            type="text"
            x-model="recherche"
            placeholder="Rechercher nom, matricule, conseil..."
            class="age-search">
    </div>

    {{-- 3. TABLEAU COMPLET PLEIN ÉCRAN --}}
    <div x-show="filtre !== 'synthese'" class="age-table-wrap">
        <table class="age-table">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Conseil</th>
                    <th>Section</th>
                    <th>Matricule</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Âge</th>
                    <th>Genre</th>
                    <th>Téléphone</th>
                    <th>Profession</th>
                    <th>Niveau Étude</th>
                    <th>Adresse</th>
                    <th>Type Pièce</th>
                    <th>N° Pièce</th>
                    <th>Parent / Contact</th>
                </tr>
            </thead>
            <tbody>
                @forelse($membres as $m)
                @php
                $texteLigne = mb_strtolower(trim($m->Conseil . ' ' . $m->Section . ' ' . $m->internal_id . ' ' . $m->Prenom . ' ' . $m->Nom . ' ' . $m->Telephone));
                @endphp
                <tr x-show="(filtre === 'tous' || filtre === '{{ $m->categorie_age }}') && ('{{ addslashes($texteLigne) }}'.includes(recherche.toLowerCase()))">
                    <td>
                        @if($m->categorie_age === 'ENFANT')
                        <span class="cat-badge cat-enfant">ENFANT</span>
                        @elseif($m->categorie_age === 'JEUNE')
                        <span class="cat-badge cat-jeune">JEUNE</span>
                        @else
                        <span class="cat-badge cat-adulte">ADULTE</span>
                        @endif
                    </td>
                    <td><strong>{{ $m->Conseil ?: '-' }}</strong></td>
                    <td><strong>{{ $m->Section ?: '-' }}</strong></td>
                    <td>{{ $m->internal_id ?: '-' }}</td>
                    <td>{{ $m->Prenom }}</td>
                    <td><strong>{{ $m->Nom }}</strong></td>
                    <td><strong>{{ $m->Age }} ans</strong></td>
                    <td>{{ $m->Genre ?: '-' }}</td>
                    <td>{{ $m->Telephone ?: '-' }}</td>
                    <td>{{ $m->Profession ?: '-' }}</td>
                    <td>{{ $m->Niveau_Etude ?: '-' }}</td>
                    <td>{{ $m->Adresse ?: '-' }}</td>
                    <td>{{ $m->Type_piece_identite ?: '-' }}</td>
                    <td>{{ $m->Numero_piece_identite ?: '-' }}</td>
                    <td>{{ $m->Parent ? $m->Parent . ' (' . $m->Contact . ')' : ($m->Contact ?: '-') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" style="text-align: center; padding: 20px;">Aucun membre enregistré.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 4. ONGLET SYNTHÈSE PAR CONSEIL ET PAR SECTION --}}
    <div x-show="filtre === 'synthese'" style="display: grid; gap: 20px;">
        <div class="age-table-wrap">
            <table class="age-table">
                <thead>
                    <tr>
                        <th>Conseil</th>
                        <th>Enfants (0-17)</th>
                        <th>Jeunes (18-35)</th>
                        <th>Adultes (36+)</th>
                        <th>Total Conseil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parConseil as $c)
                    <tr>
                        <td><strong>{{ $c->Conseil ?: 'Non défini' }}</strong></td>
                        <td>{{ $c->enfants }}</td>
                        <td>{{ $c->jeunes }}</td>
                        <td>{{ $c->adultes }}</td>
                        <td><strong>{{ $c->total }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="age-table-wrap">
            <table class="age-table">
                <thead>
                    <tr>
                        <th>Conseil</th>
                        <th>Section</th>
                        <th>Enfants (0-17)</th>
                        <th>Jeunes (18-35)</th>
                        <th>Adultes (36+)</th>
                        <th>Total Section</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parSection as $s)
                    <tr>
                        <td>{{ $s->Conseil ?: 'Non défini' }}</td>
                        <td><strong>{{ $s->Section ?: 'Non définie' }}</strong></td>
                        <td>{{ $s->enfants }}</td>
                        <td>{{ $s->jeunes }}</td>
                        <td>{{ $s->adultes }}</td>
                        <td><strong>{{ $s->total }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>