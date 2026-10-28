<?php
declare(strict_types=1);

final class Status
{
    public const NEW       = 1;
    public const IN_WORK   = 2; 
    public const DONE      = 3; 
    public const CANCELLED = 4; 

    public const TRANSITIONS = [
        self::NEW       => [self::IN_WORK, self::CANCELLED],
        self::IN_WORK   => [self::DONE, self::CANCELLED],
        self::DONE      => [],
        self::CANCELLED => [],
    ];

    public static function canTransition(int $from, int $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }
}