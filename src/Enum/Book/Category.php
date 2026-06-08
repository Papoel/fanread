<?php

declare(strict_types=1);

namespace App\Enum\Book;

enum Category: string
{
    // Fiction
    case Fiction           = 'fiction';
    case ScienceFiction    = 'science_fiction';
    case Fantasy           = 'fantasy';
    case Thriller          = 'thriller';
    case Horror            = 'horreur';
    case Mystery           = 'policier';
    case Romance           = 'romance';
    case HistoricalFiction = 'fiction_historique';
    case Adventure         = 'aventure';
    case YoungAdult        = 'young_adult';
    case GraphicNovel      = 'bande_dessinee';

        // Non-fiction
    case Biography         = 'biographie';
    case Autobiography     = 'autobiographie';
    case History           = 'histoire';
    case Science           = 'science';
    case Mathematics       = 'mathematiques';
    case Technology        = 'technologie';
    case SelfHelp          = 'developpement_personnel';
    case Psychology        = 'psychologie';
    case Philosophy        = 'philosophie';
    case Politics          = 'politique';
    case Economics         = 'economie';
    case Business          = 'entreprise';
    case Law               = 'droit';
    case Religion          = 'religion';
    case Spirituality      = 'spiritualite';

        // Arts & Culture
    case Art               = 'art';
    case Music             = 'musique';
    case Cinema            = 'cinema';
    case Architecture      = 'architecture';
    case Photography       = 'photographie';

        // Littérature & Langue
    case Literature        = 'litterature';
    case Poetry            = 'poesie';
    case Drama             = 'theatre';
    case Language          = 'linguistique';
    case Essay             = 'essai';

        // Pratique
    case Cooking           = 'cuisine';
    case Travel            = 'voyage';
    case Sports            = 'sport';
    case Health            = 'sante';
    case Nature            = 'nature';
    case Education         = 'education';

        // Autre
    case Comics            = 'comics';
    case Manga             = 'manga';
    case Other             = 'autre';
    case Unknown           = 'inconnu';
    case Unclassified      = 'non_classe';
}
