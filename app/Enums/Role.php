<?php

namespace App\Enums;

enum Role: string
{
    case SAdmin = 'sadmin';
    case Director = 'director';
    case Admin = 'admin';
    case Manager = 'manager';
    case Teacher = 'teacher';
    case Operator = 'operator';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::SAdmin => 'sAdmin',
            self::Director => 'Direktor',
            self::Admin => 'Admin',
            self::Manager => 'Menejer',
            self::Teacher => "O'qituvchi",
            self::Operator => 'Operator',
            self::Student => "O'quvchi",
        };
    }

    /** v14: o'quvchi ham veb-saytga kiradi (alohida «O'quvchi kabineti» bo'limi), xodimlar - boshqaruv paneliga. */
    public function canUsePanel(): bool
    {
        return $this !== self::Student;
    }

    /** v14: filialga bog'lanmagan, barcha filiallarni ko'ra oladigan rollar (sAdmin va Direktor). */
    public function isGlobal(): bool
    {
        return in_array($this, [self::SAdmin, self::Director], true);
    }

    /** Ruxsatlari alohida belgilanadigan rollar. */
    public function hasConfigurablePermissions(): bool
    {
        return in_array($this, [self::Director, self::Admin, self::Manager, self::Teacher, self::Operator], true);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SAdmin => 'badge-red',
            self::Director => 'badge-green',
            self::Admin => 'badge-dark',
            self::Manager => 'badge-blue',
            self::Teacher => 'badge-amber',
            self::Operator => 'badge-purple',
            self::Student => 'badge-gray',
        };
    }
}
