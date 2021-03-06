<?php

/**
 * Text Color Writer
 *
 * Thanks Thijs Lensselink
 *
 * @link http://blog.lenss.nl/2012/05/adding-colors-to-php-cli-script-output/
 */
class TextColorWriter
{
    const BASH_PROMPT_BLACK  = 30;
    const BASH_PROMPT_RED    = 31;
    const BASH_PROMPT_GREEN  = 32;
    const BASH_PROMPT_YELLOW = 33;
    const BASH_PROMPT_BLUE   = 34;
    const BASH_PROMPT_CYAN   = 36;
    const BASH_PROMPT_WHITE  = 37;

    /**
     * Format given string in chosen color
     *
     * @param  string $string
     * @param  int    $colorID
     *
     * @return string
     */
    public static function textColor($string, $colorID)
    {
        if (!in_array($colorID, static::getKnownColors())) {
            throw new Exception("Error unknown color ID $colorID");
        }

        $colorChar     = "\033[" . $colorID . "m";
        $coloredString = $colorChar . $string . "\033[0m";

        return $coloredString;
    }

    /**
     * Get allowed colors
     *
     * @return array
     */
    private static function getKnownColors()
    {
        $colors = array(
            static::BASH_PROMPT_BLACK,
            static::BASH_PROMPT_RED,
            static::BASH_PROMPT_GREEN,
            static::BASH_PROMPT_YELLOW,
            static::BASH_PROMPT_BLUE,
            static::BASH_PROMPT_WHITE,
            static::BASH_PROMPT_CYAN,
        );

        return $colors;
    }
}
