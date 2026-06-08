<?php

namespace App\Enum\Book;

enum Status: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Archived  = 'archived';
}
