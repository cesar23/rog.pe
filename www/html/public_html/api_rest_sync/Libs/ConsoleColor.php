<?php
namespace Libs;

class ConsoleColor
{
    // Colores de texto
    const BLACK = '0;30';
    const RED = '0;31';
    const GREEN = '0;32';
    const YELLOW = '0;33';
    const BLUE = '0;34';
    const PURPLE = '0;35';
    const CYAN = '0;36';
    const WHITE = '0;37';

    // Colores de texto en negrita
    const BOLD_BLACK = '1;30';
    const BOLD_RED = '1;31';
    const BOLD_GREEN = '1;32';
    const BOLD_YELLOW = '1;33';
    const BOLD_BLUE = '1;34';
    const BOLD_PURPLE = '1;35';
    const BOLD_CYAN = '1;36';
    const BOLD_WHITE = '1;37';

    // Métodos para mostrar texto en color
    public static function colorize($text, $color)
    {
        return "\033[" . $color . "m" . $text . "\033[0m";
    }

    public static function black($text)
    {
        return self::colorize($text, self::BLACK);
    }

    public static function red($text)
    {
        return self::colorize($text, self::RED);
    }

    public static function green($text)
    {
        return self::colorize($text, self::GREEN);
    }

    public static function yellow($text)
    {
        return self::colorize($text, self::YELLOW);
    }

    public static function blue($text)
    {
        return self::colorize($text, self::BLUE);
    }

    public static function purple($text)
    {
        return self::colorize($text, self::PURPLE);
    }

    public static function cyan($text)
    {
        return self::colorize($text, self::CYAN);
    }

    public static function white($text)
    {
        return self::colorize($text, self::WHITE);
    }

    // Métodos para texto en negrita
    public static function boldBlack($text)
    {
        return self::colorize($text, self::BOLD_BLACK);
    }

    public static function boldRed($text)
    {
        return self::colorize($text, self::BOLD_RED);
    }

    public static function boldGreen($text)
    {
        return self::colorize($text, self::BOLD_GREEN);
    }

    public static function boldYellow($text)
    {
        return self::colorize($text, self::BOLD_YELLOW);
    }

    public static function boldBlue($text)
    {
        return self::colorize($text, self::BOLD_BLUE);
    }

    public static function boldPurple($text)
    {
        return self::colorize($text, self::BOLD_PURPLE);
    }

    public static function boldCyan($text)
    {
        return self::colorize($text, self::BOLD_CYAN);
    }

    public static function boldWhite($text)
    {
        return self::colorize($text, self::BOLD_WHITE);
    }
}
