<?php

namespace App\Enum\Book;

enum Status: string
{
    case NotStarted = 'non_commence';
    case InProgress = 'en_cours';
    case Finish = 'termine';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Non commencé',
            self::InProgress => 'En cours',
            self::Finish => 'Terminé',
        };
    }
}
