<?php

namespace DBCast\helpers;

/**
 * Class ConsoleHelper
 * @package DBCast\helpers
 */
class ConsoleHelper
{


    /**
     * Console check
     * @return bool
     */
    static function isConsole()
    {
        return PHP_SAPI == 'cli';
    }


    /**
     * Send beep sound
     */
    static function beep()
    {
        echo "\007";
    }


    /**
     * Get colorful text
     * @param        $text
     * @param string $color
     * @return string
     */
    static function getColor($text, $color = 'white')
    {
        $colors = array(
            'green'     => '32m',
            'yellow'    => '33m',
            'white'     => '37m',
            'blue'      => '34m',
            'purple'    => '35m',
            'red'       => '31m',
            'whiteBlue' => '36m',
        );
        $color  = (!empty($colors[$color]) ? $color : 'white');

        return "\033[" . $colors[$color] . $text . "\033[" . $colors['white'];
    }


    /**
     * Asks user to confirm by typing y or n.
     *
     * @param string  $message to print out before waiting for user input
     * @param boolean $default this value is returned if no selection is made.
     * @return boolean whether user confirmed
     */
    public static function confirm($message, $default = false)
    {
        while (true) {
            $message .= ' (yes|no) [' . ($default ? 'yes' : 'no') . ']:';
            static::log($message);
            $input = trim(static::stdin());

            if (empty($input)) {
                return $default;
            }

            if (!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')) {
                return true;
            }

            if (!strcasecmp($input, 'n') || !strcasecmp($input, 'no')) {
                return false;
            }
        }

        return false;
    }


    /**
     * Log
     * @param $text
     * @param $level
     */
    static function log($text, $level = '')
    {
        $text = (is_array($text) || is_object($text)) ? print_r($text, true) : $text;
        $text = is_bool($text) ? 'bool(' . (($text == true) ? 'true' : 'false') . ')' : $text;

        if (static::isConsole()) {
            switch ($level) {
                case    '-1':
                    $text = "\n\n" . '      &   ' . $text;
                    $text = static::getColor($text, 'red');
                    static::beep();
                    break;
                case    '0':
                    $text = "\n" . '      &   ' . $text;
                    $text = static::getColor($text, 'purple');
                    break;
                case    '1':
                    $text = "\n" . '########### ' . $text . ' ###########';
                    $text = static::getColor($text, 'green');
                    break;
                case    '2':
                    $text = '   @@@@ ' . $text . ' @@@';
                    $text = static::getColor($text, 'yellow');
                    break;
                default:
                    $text = "      " . $text;
                    $text = static::getColor($text, 'whiteBlue');
            }
            static::stdout($text . "\n");
        }
    }

    /**
     * Prints a string to STDOUT.
     *
     * @param string $string the string to print
     * @return integer|boolean Number of bytes printed or false on error
     */
    public static function stdout($string)
    {
        return fwrite(\STDOUT, $string);
    }

    /**
     * Gets input from STDIN and returns a string right-trimmed for EOLs.
     *
     * @param boolean $raw If set to true, returns the raw string without trimming
     * @return string the string read from stdin
     */
    public static function stdin($raw = false)
    {
        return $raw ? fgets(\STDIN) : rtrim(fgets(\STDIN), PHP_EOL);
    }


}