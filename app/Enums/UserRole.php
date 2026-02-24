<?php

namespace App\Enums;

enum UserRole: string
{
    case PROFESSOR = 'professor';
    case ALUNO = 'aluno';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::PROFESSOR => 'Professor',
            self::ALUNO => 'Aluno',
            self::ADMIN => 'Administrador',
        };
    }

    public static function options(): array
    {
        return [
            self::PROFESSOR->value => self::PROFESSOR->label(),
            self::ALUNO->value => self::ALUNO->label(),
            self::ADMIN->value => self::ADMIN->label(),
        ];
    }
}