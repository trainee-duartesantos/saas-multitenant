<?php

namespace App\Enums;

enum TenantRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case VIEWER = 'viewer';

    public function isOwner(): bool
    {
        return $this === self::OWNER;
    }

    public function isMember(): bool
    {
        return $this === self::MEMBER;
    }
}
