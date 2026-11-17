<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conseil extends Model
{
    protected $fillable = [

        'Nom',
        'Code',
        'PF1',
        'tel_pf1',
        'PF2',
        'tel_pf2',
        'PF3',
        'tel_pf3',


    ];

    /**
     * Obtenir toutes les sections rattachées à ce conseil.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'conseil_id');
    }
}
