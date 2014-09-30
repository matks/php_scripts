<?php

/**
 * Explode Url
 *
 * Explode an URL and display its elements
 */

function echoParameter($parameter, $urldecode = false) {
	if (!$urldecode) {
		echo '   * ' . $parameter . PHP_EOL;
	} else {
		echo '   * ' . urldecode($parameter) . PHP_EOL;
	}
}

function validateInput($argv) {
	$isValid = false;

	if ((count($argv) === 3) && ($argv[2] === '--urldecode')) {
		$isValid = true;
	} else if (count($argv) === 2) {
		$isValid = true;
	}

	if (!$isValid) {
		echo 'php explore_url <url> [--urldecode]' . PHP_EOL;
		die(1);
	}
}

validateInput($argv);

require 'Util/TextColorWriter.php';

$url = $argv[1];
$GETParts = explode('&', $url);
$pathParts = explode('?', $GETParts[0]);

if (count($pathParts) !== 2) {
	throw new Exception("Error while parsing url");
}

$path = $pathParts[0];
$firstParameter = $pathParts[1];
$parameters = array_slice($GETParts, 1);

echo TextColorWriter::textColor('URL ANALYSIS:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo TextColorWriter::textColor('Path:', TextColorWriter::BASH_PROMPT_BLUE). ' '. $path . PHP_EOL;
echo TextColorWriter::textColor('GET parameters:', TextColorWriter::BASH_PROMPT_BLUE). PHP_EOL;

$urldecode = isset($argv[2]);

echo echoParameter($firstParameter, $urldecode);
foreach ($parameters as $parameter) {
	echoParameter($parameter, $urldecode);
}