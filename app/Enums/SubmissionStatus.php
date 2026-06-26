<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case New = 'new';
    case InProgress = 'processing';
    case Done = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новое',
            self::InProgress => 'В обработке',
            self::Done => 'Закрыто',
        };
    }
}
