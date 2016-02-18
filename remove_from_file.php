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
    if (count($argv) !== 4) {
        echo 'php remove_from_file <file1> <file2> <output>' . PHP_EOL;
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
 *
 * @return array blacklist is in keys
 * @throws Exception
 */
function computeBlackListFromFile($filepath)
{
    $handle = fopen($filepath, "r");

    if (false === $handle) {
        throw new \Exception("Could not open file $filepath");
    }

    $blackList = array();

    while (($line = fgets($handle)) !== false) {
        $blackList[trim($line)] = true;
    }

    fclose($handle);

    return $blackList;
}

validateInput($argv);

require 'Util/TextColorWriter.php';

$filepath1 = $argv[1];
$filepath2 = $argv[2];
$outputFilepath = $argv[3];

$blackList = computeBlackListFromFile($filepath2);

$fileHandle1 = fopen($filepath1, "r");
$outputFile = fopen($outputFilepath, 'w');

if (false === $fileHandle1) {
    throw new \Exception("Could not open file $fileHandle1");
}
if (false === $outputFile) {
    throw new \Exception("Could not write file $outputFile");
}

$i = 0;
$removed = 0;
while (($line = fgets($fileHandle1)) !== false) {

    $isNotInBlacklist = (false === isset($blackList[trim($line)]));

    if ($isNotInBlacklist) {
        fwrite($outputFile, $line);
    } else {
        $removed++;
    }

    $i++;
}

fclose($fileHandle1);
fclose($outputFile);

// echo result
echo TextColorWriter::textColor('Done', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo TextColorWriter::textColor("Processed : $i lines", TextColorWriter::BASH_PROMPT_CYAN) . PHP_EOL;
echo TextColorWriter::textColor("Removed : $removed lines", TextColorWriter::BASH_PROMPT_CYAN) . PHP_EOL;
