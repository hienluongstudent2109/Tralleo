<?php

namespace App\Enums;

enum WorkspaceRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public static function assignable(): array
    {
        return [self::Admin->value, self::Member->value];
    }
}
