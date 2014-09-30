<?php

/**
 * Explode Url
 *
 * Explode an URL and display its elements
 */

if (count($argv) !== 2) {
	echo 'php explore_url <url>' . PHP_EOL;
	die(1);
}

require 'Util/TextColorWriter.php';

$url = $argv[1];
$GETParts = explode('&', $url);
$pathParts = explode('?', $GETParts[0]);

if (count($pathParts) !== 2) {
	throw new Exception("Error while parsing url");
}

$path = $pathParts[0];
$firstParameter = $GETParts[1];

echo TextColorWriter::textColor('URL ANALYSIS:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo TextColorWriter::textColor('Path:', TextColorWriter::BASH_PROMPT_BLUE). ' '. $path . PHP_EOL;
echo TextColorWriter::textColor('GET parameters:', TextColorWriter::BASH_PROMPT_BLUE). PHP_EOL;
echo '   * ' . $firstParameter . PHP_EOL;
foreach ($pathParts as $parameter) {
	echo '   * ' . $parameter . PHP_EOL;
}