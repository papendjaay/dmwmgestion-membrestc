<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function formatNumero(string $telephone): string
    {
        $numero = preg_replace('/[^0-9]/', '', $telephone);

        if (strlen($numero) === 9) {
            return '221' . $numero;
        }

        return $numero;
    }

    public function envoyerTemplate(string $telephone, string $templateName, array $parametres = [], string $langue = 'fr'): bool
    {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_number_id');
        $version = config('services.whatsapp.version', 'v21.0');

        // Ignore silencieusement si la config est vide ou contient encore les valeurs d'exemple
        if (
            empty($token) ||
            empty($phoneId) ||
            str_contains($token, 'votre_jeton') ||
            str_contains($phoneId, 'votre_id')
        ) {
            Log::info('Envoi WhatsApp ignoré : clés Meta non configurées dans .env.');
            return false;
        }

        $destinataire = $this->formatNumero($telephone);

        $components = [];
        if (! empty($parametres)) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn($valeur) => [
                    'type' => 'text',
                    'text' => (string) $valeur,
                ], $parametres),
            ];
        }

        try {
            // Notez bien le slash "/" entre ://facebook.com et {$version}
            $url = "https://://facebook.com/{$version}/{$phoneId}/messages";

            $response = Http::withToken($token)
                ->timeout(10)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $destinataire,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => ['code' => $langue],
                        'components' => $components,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Échec envoi WhatsApp API', [
                    'destinataire' => $destinataire,
                    'statut' => $response->status(),
                    'erreur' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Exception WhatsApp API : ' . $e->getMessage());
            return false;
        }
    }
}
