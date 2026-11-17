<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\WelcomeBannerWidget;
use App\Filament\Widgets\MembresParConseilChart;
use App\Filament\Widgets\MembresParGenreChart;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $title = 'Tableau de bord';

    public function getColumns(): int | array
    {
        return 2; // Nombre de colonnes sur grand écran
    }
}
