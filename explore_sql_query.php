<?php

/**
 * @param $length
 *
 * @return string
 */
function buildSpaceString($length)
{
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= ' ';
    }

    return $string;
}

/**
 * Echo array of strings in output
 *
 * @param array $elements
 * @param int   $tabLength
 */
function echoArrayAsString(array $elements, $tabLength)
{
    $tab = buildSpaceString($tabLength + 1);

    $elementsAsString      = implode(' ', $elements);
    $commaSeparatedStrings = explode(',', $elementsAsString);

    $length = count($commaSeparatedStrings);

    if ($length === 1) {
        echo ' ' . $commaSeparatedStrings[0] . PHP_EOL;

        return;
    }

    for ($i = 0; $i < $length; $i++) {

        if ($i === 0) {
            echo ' ' . $commaSeparatedStrings[$i] . ',' . PHP_EOL;
        } else if ($i === ($length - 1)) {
            echo $tab . $commaSeparatedStrings[$i] . PHP_EOL;
        } else {
            echo $tab . $commaSeparatedStrings[$i] . ',' . PHP_EOL;
        }
    }
}

/**
 * @param string $word
 *
 * @return bool
 */
function isSpecificSingleKeyWord($word)
{
    $keyWords = array(
        'SELECT',
        'FROM',
        'JOIN',
        'WHERE',
        'AND',
        'OR',
        'HAVING',
        'LIKE',
    );

    if (in_array($word, $keyWords)) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param string $word
 *
 * @return bool
 */
function isSpecificDoubleKeyWord($word)
{
    $keyWords = array(
        'INNER JOIN',
        'LEFT JOIN',
        'RIGHT JOIN',
        'ORDER BY',
        'GROUP BY',
    );

    if (in_array($word, $keyWords)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Attempt to gather words in 'SQL parts' (for ex. all SELECT all together)
 *
 * @param array $words
 *
 * @return array
 */
function sortWordsInParts(array $words)
{
    // variables init
    $parts   = array();
    $keyWord = '';
    $part    = array();

    $length = count($words);

    for ($i = 0; $i < $length; $i++) {

        $currentWord = $words[$i];

        if ($i == ($length - 1)) {
            $nextWord = '';
        } else {
            $nextWord = $words[$i + 1];
        }

        $doubleWord = $currentWord . ' ' . $nextWord;

        if (isSpecificSingleKeyWord($currentWord)) {

            // found key word: gather all previous words in a part

            $parts[] = [$keyWord, $part];
            $keyWord = $currentWord;
            $part    = array();
        } else if (isSpecificDoubleKeyWord($doubleWord)) {

            // found key double word: gather all previous words in a part

            $parts[] = [$keyWord, $part];
            $keyWord = $doubleWord;
            $part    = array();

            // skip next word as it is part of the double word
            $i++;
        } else {

            // keep adding words in the current part

            $part[] = $currentWord;
        }
    }

    $parts[] = [$keyWord, $part];

    return $parts;
}

/**
 * Validate console input
 *
 * Die if input is not valid
 *
 * @param  array $argv user inputs
 */
function validateInput($argv)
{
    $isValid = false;

    if (count($argv) === 2) {
        $isValid = true;
    }

    if (!$isValid) {
        echo 'php explore_sql_query <query>' . PHP_EOL;
        die(1);
    }
}

validateInput($argv);

require 'Util/TextColorWriter.php';

$query = $argv[1];
$query = str_replace(array("\r\n", "\r", "\n"), " ", $query);

$words      = explode(' ', $query);
$queryParts = sortWordsInParts($words);

echo TextColorWriter::textColor('QUERY ANALYSIS:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;

foreach ($queryParts as $part) {
    $key     = $part[0];
    $content = $part[1];

    $tabLength = strlen($key) + 1;

    if (in_array($key, array('AND', 'OR'))) {
        // additionnal space
        echo '   ';
    }

    echo ' ' . TextColorWriter::textColor($key, TextColorWriter::BASH_PROMPT_YELLOW) . ' ';
    echoArrayAsString($content, $tabLength);
}
