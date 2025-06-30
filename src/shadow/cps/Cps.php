<?php

namespace shadow\cps;

class Cps
{
    private static array $cps = [];

    public static function addClicks(string $playerName): void
    {
        if (!isset(self::$cps[$playerName])) {
            self::$cps[$playerName] = 0;
        }
        self::$cps[$playerName]++;
    }

    public static function removeClicks(string $playerName): void
    {
        if (self::$cps[$playerName] > 0) {
            self::$cps[$playerName]--;
        }
    
    }

    public static function getCps(string $playerName): int
    {
        return self::$cps[$playerName] ?? 0;
    }

    public static function resetCps(string $playerName): void
    {
        self::$cps[$playerName] = 0;
    }
}