<?php

declare(strict_types=1);

namespace App\Enum\Book;

enum Category: string
{
    // Fiction
    case Fiction = 'fiction';
    case ScienceFiction = 'science_fiction';
    case Fantasy = 'fantasy';
    case Thriller = 'thriller';
    case Horror = 'horreur';
    case Mystery = 'policier';
    case Romance = 'romance';
    case HistoricalFiction = 'fiction_historique';
    case Adventure = 'aventure';
    case YoungAdult = 'young_adult';
    case GraphicNovel = 'bande_dessinee';

    // Non-fiction
    case Biography = 'biographie';
    case Autobiography = 'autobiographie';
    case History = 'histoire';
    case Science = 'science';
    case Mathematics = 'mathematiques';
    case Technology = 'technologie';
    case SelfHelp = 'developpement_personnel';
    case Psychology = 'psychologie';
    case Philosophy = 'philosophie';
    case Politics = 'politique';
    case Economics = 'economie';
    case Business = 'entreprise';
    case Law = 'droit';
    case Religion = 'religion';
    case Spirituality = 'spiritualite';

    // Arts & Culture
    case Art = 'art';
    case Music = 'musique';
    case Cinema = 'cinema';
    case Architecture = 'architecture';
    case Photography = 'photographie';

    // Littérature & Langue
    case Literature = 'litterature';
    case Poetry = 'poesie';
    case Drama = 'theatre';
    case Language = 'linguistique';
    case Essay = 'essai';

    // Pratique
    case Cooking = 'cuisine';
    case Travel = 'voyage';
    case Sports = 'sport';
    case Health = 'sante';
    case Nature = 'nature';
    case Education = 'education';

    // Autre
    case Comics = 'comics';
    case Manga = 'manga';
    case Other = 'autre';
    case Unknown = 'inconnu';
    case Unclassified = 'non_classe';

    public function label(): string
    {
        return match ($this) {
            // Fiction
            self::Fiction => 'Fiction',
            self::ScienceFiction => 'Science-fiction',
            self::Fantasy => 'Fantasy',
            self::Thriller => 'Thriller',
            self::Horror => 'Horreur',
            self::Mystery => 'Policier / Mystère',
            self::Romance => 'Romance',
            self::HistoricalFiction => 'Fiction historique',
            self::Adventure => 'Aventure',
            self::YoungAdult => 'Jeunesse',
            self::GraphicNovel => 'Bande dessinée',
            // Non-fiction
            self::Biography => 'Biographie',
            self::Autobiography => 'Autobiographie',
            self::History => 'Histoire',
            self::Science => 'Science',
            self::Mathematics => 'Mathématiques',
            self::Technology => 'Technologie',
            self::SelfHelp => 'Développement personnel',
            self::Psychology => 'Psychologie',
            self::Philosophy => 'Philosophie',
            self::Politics => 'Politique',
            self::Economics => 'Économie',
            self::Business => 'Entreprise',
            self::Law => 'Droit',
            self::Religion => 'Religion',
            self::Spirituality => 'Spiritualité',
            // Arts
            self::Art => 'Art',
            self::Music => 'Musique',
            self::Cinema => 'Cinéma',
            self::Architecture => 'Architecture',
            self::Photography => 'Photographie',
            // Littérature
            self::Literature => 'Littérature',
            self::Poetry => 'Poésie',
            self::Drama => 'Théâtre',
            self::Language => 'Linguistique',
            self::Essay => 'Essai',
            // Pratique
            self::Cooking => 'Cuisine',
            self::Travel => 'Voyage',
            self::Sports => 'Sport',
            self::Health => 'Santé',
            self::Nature => 'Nature',
            self::Education => 'Éducation',
            // Autre
            self::Comics => 'Comics',
            self::Manga => 'Manga',
            self::Other => 'Autre',
            self::Unknown => 'Inconnu',
            self::Unclassified => 'Non classé',
        };
    }
}
