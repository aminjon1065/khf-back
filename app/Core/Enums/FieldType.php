<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum FieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case RichText = 'richtext';
    case Number = 'number';
    case Boolean = 'boolean';
    case Date = 'date';
    case DateTime = 'datetime';
    case Select = 'select';
    case MultiSelect = 'multiselect';
    case Media = 'media';
    case Relation = 'relation';
    case Json = 'json';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Textarea => 'Textarea',
            self::RichText => 'Rich Text',
            self::Number => 'Number',
            self::Boolean => 'Boolean',
            self::Date => 'Date',
            self::DateTime => 'Date & Time',
            self::Select => 'Select',
            self::MultiSelect => 'Multi-Select',
            self::Media => 'Media',
            self::Relation => 'Relation',
            self::Json => 'JSON',
        };
    }

    public function isTranslatableByDefault(): bool
    {
        return match ($this) {
            self::Text, self::Textarea, self::RichText => true,
            default => false,
        };
    }
}
