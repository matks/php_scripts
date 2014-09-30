<?php

/**
 * Echo GET parameter in output
 *
 * @param  string  $parameter
 * @param  boolean $urldecode flag to apply urldecode()
 */
function echoParameter($parameter, $urldecode = false)
{
    if (!$urldecode) {
        echo '   * ' . $parameter . PHP_EOL;
    } else {
        echo '   * ' . urldecode($parameter) . PHP_EOL;
    }
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

    if ((count($argv) === 3) && ($argv[2] === '--urldecode')) {
        $isValid = true;
    } elseif (count($argv) === 2) {
        $isValid = true;
    }

    if (!$isValid) {
        echo 'php explore_url <url> [--urldecode]' . PHP_EOL;
        die(1);
    }
}

validateInput($argv);

require 'Util/TextColorWriter.php';

// parse url
$url = $argv[1];
$GETParts = explode('&', $url);
$pathParts = explode('?', $GETParts[0]);

if (count($pathParts) !== 2) {
    throw new Exception('Not a valid url : found more than 1 ?');
}

$path = $pathParts[0];
$firstParameter = $pathParts[1];
$parameters = array_slice($GETParts, 1);

// echo result
echo TextColorWriter::textColor('URL ANALYSIS:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo TextColorWriter::textColor('Path:', TextColorWriter::BASH_PROMPT_BLUE). ' '. $path . PHP_EOL;
echo TextColorWriter::textColor('GET parameters:', TextColorWriter::BASH_PROMPT_BLUE). PHP_EOL;

$urldecode = isset($argv[2]);

// echo GET parameters
echo echoParameter($firstParameter, $urldecode);
foreach ($parameters as $parameter) {
    echoParameter($parameter, $urldecode);
}
