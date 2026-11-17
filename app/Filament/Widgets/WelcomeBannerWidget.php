<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\Auth\Authenticatable;

class WelcomeBannerWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome-banner-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -3;

    public function getViewData(): array
    {
        /** @var Authenticatable|\App\Models\User|null $user */
        $user = filament()->auth()->user();

        $roleName = 'Membre';
        if ($user && method_exists($user, 'getRoleNames')) {
            $role = $user->getRoleNames()->first();
            if ($role) {
                $roleName = ucfirst(str_replace('_', ' ', $role));
            }
        }

        return [
            'user' => $user,
            'role' => $roleName,
        ];
    }
}
