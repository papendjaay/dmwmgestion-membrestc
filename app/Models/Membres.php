<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membres extends Model
{
    protected $fillable = [
        'internal_id',
        'Prenom',
        'Nom',
        'Genre',
        'Age',
        'Niveau_Etude',
        'Profession',
        'Conseil',
        'Section',
        'Telephone',
        'Adresse',
        'Type_piece_identite',
        'Numero_piece_identite',
        'Parent',
        'Lien_parental',
        'Contact',
        'Cree_par',
        'Statut',
    ];
}
