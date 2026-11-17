<div class="dmww-login-wrapper">
    <style>
        .dmww-login-wrapper {
            min-height: 100vh;
            display: flex;
            background-color: #060b19;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* Colonne gauche (cachée sur mobile, 50% sur grand écran) */
        .dmww-left-panel {
            display: none;
        }

        @media (min-width: 1024px) {
            .dmww-left-panel {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                width: 50%;
                min-height: 100vh;
                position: relative;
                padding: 3rem 3.5rem;
                background-color: #060f21;
                border-right: 1px solid rgba(56, 189, 248, 0.2);
                overflow: hidden;
                box-sizing: border-box;
            }

            .dmww-mobile-brand {
                display: none !important;
            }
        }

        .dmww-brand {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dmww-brand img {
            height: 2.75rem;
            width: 2.75rem;
            border-radius: 9999px;
            object-fit: cover;
            border: 1px solid rgba(56, 189, 248, 0.5);
        }

        .dmww-brand span {
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #38bdf8;
        }

        /* Image de couverture pleine hauteur et largeur sur le panneau gauche */
        .dmww-bg-image {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .dmww-bg-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0.28;
        }

        /* Voile dégradé sombre par-dessus l'image pour garder le texte blanc très lisible */
        .dmww-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(6, 15, 33, 0.75) 0%, rgba(10, 25, 53, 0.55) 50%, rgba(6, 11, 25, 0.88) 100%);
            pointer-events: none;
        }

        .dmww-hero {
            position: relative;
            z-index: 10;
            max-width: 29rem;
        }

        .dmww-badge {
            display: inline-block;
            padding: 0.35rem 0.9rem;
            margin-bottom: 1.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #7dd3fc;
            background-color: rgba(14, 165, 233, 0.18);
            border: 1px solid rgba(56, 189, 248, 0.4);
            border-radius: 9999px;
            backdrop-filter: blur(4px);
        }

        .dmww-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: -0.025em;
            color: #ffffff;
            margin: 0 0 1rem 0;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .dmww-hero p {
            font-size: 1.025rem;
            color: #e2e8f0;
            line-height: 1.65;
            margin: 0;
        }

        .dmww-footer {
            position: relative;
            z-index: 10;
            font-size: 0.75rem;
            color: #cbd5e1;
        }

        /* Colonne droite avec dégradé bleu nuit */
        .dmww-right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background: radial-gradient(circle at top right, #0e244d 0%, #081226 48%, #040812 100%);
        }

        /* Cadre de connexion agrandi */
        .dmww-form-box {
            width: 100%;
            max-width: 31rem;
            padding: 2.75rem;
            background: rgba(13, 25, 48, 0.55);
            border: 1px solid rgba(56, 189, 248, 0.16);
            border-radius: 1.25rem;
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(8px);
        }

        .dmww-submit-btn {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0.85rem 1.5rem;
            border: none;
            border-radius: 0.6rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(90deg, #0284c7 0%, #2563eb 100%);
            box-shadow: 0 4px 18px rgba(2, 132, 199, 0.4);
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.1s ease;
        }

        .dmww-submit-btn:hover {
            opacity: 0.92;
        }

        .dmww-submit-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
    </style>

    {{-- COLONNE GAUCHE : Image pleine page, Présentation et Slogan --}}
    <div class="dmww-left-panel">
        {{-- Image de couverture couvrant tout le panneau gauche --}}
        <div class="dmww-bg-image">
            <img src="{{ asset('images/couverture.jpg') }}" alt="Couverture">
        </div>
        <div class="dmww-bg-overlay"></div>

        {{-- En-tête : Logo et Nom --}}
        <div class="dmww-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo D.M.W.W">
            <span>D.M.W.W / COMITÉ TAMBA</span>
        </div>

        {{-- Texte principal --}}
        <div class="dmww-hero">
            <span class="dmww-badge">PORTAIL OFFICIEL</span>
            <h1>VOTRE ESPACE DE GESTION CENTRALISÉ.</h1>
            <p>
                Connectez-vous pour accéder aux outils de suivi, de coordination et d'administration du Comité Tamba en toute sécurité.
            </p>
        </div>

        {{-- Pied de page --}}
        <div class="dmww-footer">
            © {{ date('Y') }} D.M.W.W / COMITÉ TAMBA • Tous droits réservés.
        </div>
    </div>

    {{-- COLONNE DROITE : Formulaire de connexion en dégradé --}}
    <div class="dmww-right-panel">
        <div class="dmww-form-box">
            {{-- Logo visible uniquement sur mobile --}}
            <div class="dmww-brand dmww-mobile-brand" style="margin-bottom: 2rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <span>D.M.W.W / COMITÉ TAMBA</span>
            </div>

            <div style="margin-bottom: 2rem;">
                <h2 style="font-size: 1.85rem; font-weight: 700; letter-spacing: -0.025em; color: #ffffff; margin: 0;">
                    Connexion
                </h2>
                <p style="margin-top: 0.6rem; font-size: 0.95rem; color: #94a3b8; line-height: 1.55;">
                    Saisissez votre adresse e-mail pour recevoir votre lien d'accès.
                </p>
            </div>

            {{-- Formulaire Filament --}}
            <form wire:submit="authenticate" style="display: flex; flex-direction: column; gap: 1.75rem;">
                {{ $this->form }}

                <button type="submit" wire:loading.attr="disabled" class="dmww-submit-btn">
                    <span wire:loading.remove wire:target="authenticate">Se connecter</span>
                    <!-- <span wire:loading.inline wire:target="authenticate">Envoi du lien en cours...</span> -->
                </button>
            </form>
        </div>
    </div>
</div>