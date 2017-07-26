<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

/**
 * Validate console input
 *
 * Die if input is not valid
 *
 * @param  array $argv user inputs
 */
function validateInput($argv)
{
    if (count($argv) !== 4) {
        echo 'php compare_hashmaps <file1> <file2> <root_node>' . PHP_EOL;
        die(1);
    }

    if (!file_exists($argv[1])) {
        echo "Error: " . $argv[1] . " is not a valid file" . PHP_EOL;
        die(1);
    }
    if (!file_exists($argv[2])) {
        echo "Error: " . $argv[2] . " is not a valid file" . PHP_EOL;
        die(1);
    }
}

/**
 * @param string $filepath
 * @param string $rootNode
 *
 * @return array
 * @throws Exception
 */
function extractArrayFromFile($filepath, $rootNode)
{
    $value = Yaml::parse(file_get_contents($filepath));

    if (!array_key_exists($rootNode, $value)) {
        $showKeys = implode(', ', array_keys($value));
        throw new \RuntimeException("Expects root node '$rootNode'' in file $filepath, got: $showKeys");
    }

    return $value[$rootNode];
}

validateInput($argv);

require 'Util/TextColorWriter.php';

$filepath1 = $argv[1];
$filepath2 = $argv[2];
$rootNode = $argv[3];

$list1 = extractArrayFromFile(realpath($filepath1), $rootNode);
$list2 = extractArrayFromFile(realpath($filepath2), $rootNode);

$inFile1ButNotInFile2 = [];
$inFile2ButNotInFile1 = [];
$sameKeyDifferentValues = [];

foreach ($list1 as $key => $value) {
    if (!array_key_exists($key, $list2)) {
        $inFile1ButNotInFile2[$key] = $value;
    } elseif ($list1[$key] !== $list2[$key]) {
        $sameKeyDifferentValues[$key] = $list1[$key] . ' / ' . $list2[$key];
    }
}
foreach ($list2 as $key => $value) {
    if (!array_key_exists($key, $list1)) {
        $inFile2ButNotInFile1[$key] = $value;
    } elseif ($list2[$key] !== $list1[$key]) {
        $sameKeyDifferentValues[$key] = $list1[$key] . ' / ' . $list2[$key];
    }
}


// echo result
echo TextColorWriter::textColor('Done', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;

if (false === empty($inFile1ButNotInFile2)) {
    echo TextColorWriter::textColor('Found in file 1 but not in file 2:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;

    foreach ($inFile1ButNotInFile2 as $key => $value) {
        echo '   - ' . $key . PHP_EOL;
    }
}

if (false === empty($inFile2ButNotInFile1)) {
    echo TextColorWriter::textColor('Found in file 2 but not in file 1:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;

    foreach ($inFile2ButNotInFile1 as $key => $value) {
        echo '   - ' . $key . PHP_EOL;
    }
}

if (false === empty($sameKeyDifferentValues)) {
    echo TextColorWriter::textColor('Key in both hashmaps but different value:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;

    foreach ($sameKeyDifferentValues as $key => $value) {
        echo '   - ' . $key . ' : ' . $value . PHP_EOL;
    }
}
