<?php

/**
 * Validate console input
 *
 * Die if input is not valid
 *
 * @param  array $argv user inputs
 */
function validateInput($argv)
{
    $isValid = (count($argv) === 2);
    if (!$isValid) {
        echo 'php explore_url <integer>' . PHP_EOL;
        die(1);
    }

    $int = intval($argv[1]);

    if (0 == $int) {
        echo 'Not an (not null) integer: ' . $argv[1] . PHP_EOL;
        die(1);
    }
}

/**
 * Compute all power of two inferior to $int
 *
 * @param int $int
 *
 * @return int[]
 */
function computeEligiblePowersOfTwo($int)
{
    $start = 0;
    $current = $start;

    $result = [];
    while ($current <= $int) {
        $result[$current] = pow(2, $current);
        $current++;
    }

    return $result;
}

validateInput($argv);

require 'Util/TextColorWriter.php';

$int = intval($argv[1]);

$eligiblePowersOfTwo = computeEligiblePowersOfTwo($int);

$result = array();
foreach ($eligiblePowersOfTwo as $exp => $power) {
    if ($power & $int) {
        $result[$exp] = $power;
    }
}

// echo result
echo TextColorWriter::textColor('INTEGER ANALYSIS:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo $int . ' = ';
foreach ($result as $power) {
    echo $power . ' + ';
}
echo '(0)';
echo PHP_EOL;
echo $int . ' = ';
foreach ($result as $exp => $power) {
    echo '2^' . $exp . ' + ';
}
echo '(0)';
echo PHP_EOL;