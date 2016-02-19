<?php

// credentials file
require 'config.php';

/**
 * @param array $config
 *
 * @return PDO
 */
function getPDO(array $config)
{
    $pdo = new PDO(
        sprintf(
            '%s:host=%s;port=%d;dbname=%s',
            $config['driver'],
            $config['hostname'],
            $config['port'],
            $config['database']
        ),
        $config['user'],
        $config['password']
    );

    return $pdo;
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
    if (count($argv) !== 3) {
        echo 'php get_by_sql_value.php <input> <output>' . PHP_EOL;
        die(1);
    }

    if (!file_exists($argv[1])) {
        echo "Error: " . $argv[1] . " is not a valid file" . PHP_EOL;
        die(1);
    }
}

/**
 * @param int $bufferCount
 *
 * @return bool
 */
function bufferIsNotFull($bufferCount)
{
    return ($bufferCount < 1000);
}

/**
 * @param string   $select
 * @param string   $table
 * @param string   $where
 * @param array    $buffer
 * @param PDO      $pdo
 * @param resource $outputFile
 *
 * @return int
 */
function processBuffer($select, $table, $where, $buffer, $pdo, $outputFile)
{
    $values = implode("','", $buffer);
    $query = "SELECT $select from $table WHERE $where IN ('$values')";

    $stmt1 = $pdo->query($query);
    $lost = 0;

    $result = array();
    while ($line = $stmt1->fetch(\PDO::FETCH_NUM)) {
        if (!$stmt1) {
            throw new RuntimeException(join(' : ', $pdo->errorInfo()));
        }

        if (!is_array($line)) {
            throw new RuntimeException("Expects array as result, got $line");
        }

        $result[] = current($line);
    }

    writeResultInOutputFile($result, $outputFile);

    if (count($result) !== count($buffer)) {
        return ($lost + count($buffer) - count($result));
    } else {
        return $lost;
    }
}

/**
 * @param array    $values
 * @param resource $file
 */
function writeResultInOutputFile(array $values, $file)
{
    foreach ($values as $value) {
        fwrite($file, $value . "\n");
    }
}

validateInput($argv);

$filepath = $argv[1];

require 'Util/TextColorWriter.php';

$pdo = getPDO($config);
$pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

$inputFilepath = $argv[1];
$outputFilepath = $argv[2];

$inputFile = fopen($inputFilepath, "r");
$outputFile = fopen($outputFilepath, 'w');

if (false === $inputFile) {
    throw new \Exception("Could not open file $inputFile");
}
if (false === $outputFile) {
    throw new \Exception("Could not write file $outputFile");
}

$buffer = array();

$i = 0;
$lost = 0;
$queriesRun = 0;

while (($rawLine = fgets($inputFile)) !== false) {

    $cleanLine = str_replace('"', '', trim($rawLine));

    if (bufferIsNotFull(count($buffer))) {
        $buffer[] = $cleanLine;
        $i++;
        continue;
    }

    $buffer[] = $cleanLine;
    $queriesRun++;
    $lostLines = processBuffer($select, $table, $where, $buffer, $pdo, $outputFile);

    $lost += $lostLines;

    // reset buffer
    $bufferCount = 0;
    $buffer = array();

    $i++;
}

// process what's left in buffer
$queriesRun++;
$lostLines = processBuffer($select, $table, $where, $buffer, $pdo, $outputFile);
$lost += $lostLines;


fclose($inputFile);
fclose($outputFile);

// echo result
echo TextColorWriter::textColor('Done', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
echo TextColorWriter::textColor("Processed : $i lines", TextColorWriter::BASH_PROMPT_CYAN) . PHP_EOL;
echo TextColorWriter::textColor("Lost : $lost lines", TextColorWriter::BASH_PROMPT_CYAN) . PHP_EOL;
echo TextColorWriter::textColor("SQL : $queriesRun queries", TextColorWriter::BASH_PROMPT_CYAN) . PHP_EOL;
