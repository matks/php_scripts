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
    $isValid = false;

    if (count($argv) === 1) {
        $isValid = true;
    }

    if (!$isValid) {
        echo 'php compare_sql_tables.php' . PHP_EOL;
        die(1);
    }
}

validateInput($argv);

require 'Util/TextColorWriter.php';

$pdo1 = getPDO($config);
$pdo2 = getPDO($config);

// implement these functions in config.php file
$orderedColumns = getOrderedColumns();
$orderBy = getOrderBy();

$query1 = "SELECT $orderedColumns from $table1 ORDER BY $orderBy";
$query2 = "SELECT $orderedColumns from $table2 ORDER BY $orderBy";

$pdo1->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
$stmt1 = $pdo1->query($query1);

if (!$stmt1) {
    throw new \RuntimeException(join(' : ', $pdo1->errorInfo()));
}

$pdo2->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
$stmt2 = $pdo2->query($query2);

if (!$stmt1) {
    throw new \RuntimeException(join(' : ', $pdo2->errorInfo()));
}

$errors = array();
$i = 0;
while ($line = $stmt1->fetch(\PDO::FETCH_NUM)) {
    if (!$stmt1) {
        throw new RuntimeException(join(' : ', $pdo1->errorInfo()));
    }

    $otherLine = $stmt2->fetch(\PDO::FETCH_NUM);
    if (!$stmt2) {
        throw new RuntimeException(join(' : ', $pdo2->errorInfo()));
    }

    $string1 = implode(';', $line);
    $string2 = implode(';', $otherLine);

    if ($string1 != $string2) {
       $errors[] = $string1 . ' / ' . $string2;
    }

    $i++;
    echo "\033[G$i lines analysed";
}
echo PHP_EOL;

$stmt1->closeCursor();
$stmt2->closeCursor();


// echo result
echo TextColorWriter::textColor('SQL TABLE COMPARISON:', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;

if (empty($errors)) {
    echo TextColorWriter::textColor('Table content is identical', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
} else {
    echo TextColorWriter::textColor(count($errors) . 'different lines :', TextColorWriter::BASH_PROMPT_GREEN) . PHP_EOL;
    foreach($errors as $e) {
        echo "$e" . PHP_EOL;
    }
}
