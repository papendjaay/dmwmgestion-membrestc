<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lien d'accès</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 560px; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                    {{-- En-tête bleu nuit assorti à votre application --}}
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #091834 0%, #0d254e 100%); padding: 28px 20px;">
                            <span style="font-size: 16px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #38bdf8;">
                                {{ config('app.name') }}
                            </span>
                        </td>
                    </tr>

                    {{-- Corps du message --}}
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h1 style="margin: 0 0 16px 0; font-size: 22px; font-weight: 700; color: #0f172a;">
                                Bonjour !
                            </h1>

                            <p style="margin: 0 0 28px 0; font-size: 15px; line-height: 1.6; color: #475569;">
                                Cliquez sur le bouton ci-dessous pour vous connecter directement à votre espace :
                            </p>

                            <div style="text-align: center; margin: 32px 0;">
                                <a href="{{ $url }}" target="_blank" style="display: inline-block; background-color: #0284c7; color: #ffffff; font-weight: 600; font-size: 15px; text-decoration: none; padding: 14px 32px; border-radius: 8px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);">
                                    Accéder à l'application
                                </a>
                            </div>

                            <p style="margin: 28px 0 0 0; font-size: 14px; line-height: 1.6; color: #64748b;">
                                Ce lien est valable pendant <strong>10 minutes</strong>. Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail.
                            </p>

                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 28px 0;">

                            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Cordialement,<br>
                                <strong style="color: #0f172a;">{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Pied de page --}}
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 18px 20px; border-top: 1px solid #e2e8f0;">
                            <span style="font-size: 12px; color: #94a3b8;">
                                © {{ date('Y') }} {{ config('app.name') }} • Tous droits réservés.
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>