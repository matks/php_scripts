php_scripts
===========

Some useful php scripts.

explore_url
-----------

```bash
php explore_url.php <url> [--urldecode]
```
Explode an URL in different parts to ease reading

explore_sql_query
-----------------

```bash
php explore_sql_query.php <query>
```
Display a SQL query formatted in a more readable way

compare_sql_tables
------------------

```bash
php compare_sql_tables.php
```
Compare two SQL tables to check whether their content is identical

Add settings/credentials in a config.php file to run it

remove_from_file
------------------

```bash
php remove_from_file.php file1 file2 output
```
Compare two text files and remove from file1 the lines from file2

get_by_sql_value
----------------

```bash
php get_by_sql_value.php inputFile outputFile
```
Read input file, get lines from an SQL table from file1 value, write lines into output file

Add settings/credentials in a config.php file to run it

find_binary
-----------

```bash
php find_binary.php <integer>
```
Decompose an integer into powers of two

compare_hashmaps
----------------

```bash
php compare_yaml_hashmaps.php file1 file2 <root_node>
```
Compare 2 YAML hashmap files (such as symfony parameters.yml files)

Example: `$ php compare_yaml_hashmaps.php parameters1.yml parameters2.yml 'parameters'`