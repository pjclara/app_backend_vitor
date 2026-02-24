<?php

namespace App\Enums;

enum DictationDifficulty: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function label(): string
    {
        return match($this) {
            self::EASY => 'Fácil',
            self::MEDIUM => 'Médio',
            self::HARD => 'Difícil',
        };
    }

    public static function options(): array
    {
        return [
            self::EASY->value => self::EASY->label(),
            self::MEDIUM->value => self::MEDIUM->label(),
            self::HARD->value => self::HARD->label(),
        ];
    }
}