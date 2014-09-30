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

    if (!filter_var($argv[1], FILTER_VALIDATE_URL)) {
        echo 'Bad url: ' . $argv[1] . PHP_EOL;
        die(1);
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

$path = $pathParts[0];
$firstParameter = $pathParts[1];
$moreParameters = array_slice($GETParts, 1);
$parameters = array_merge(array($firstParameter), $moreParameters);

// echo result
echo TextColorWriter::textColor('URL ANALYSIS:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo TextColorWriter::textColor('Path:', TextColorWriter::BASH_PROMPT_BLUE). ' '. $path . PHP_EOL;
echo TextColorWriter::textColor('GET parameters:', TextColorWriter::BASH_PROMPT_BLUE). PHP_EOL;

$urldecode = isset($argv[2]);

// echo GET parameters
foreach ($parameters as $parameter) {
    echoParameter($parameter, $urldecode);
}
