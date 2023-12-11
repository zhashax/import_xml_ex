<?php

$xmlFilePath = 'file.xml';// xml файл

$xml = simplexml_load_file($xmlFilePath);

$hostname = 'localhost'; 
$username = 'root';
$password = '';
$database = 'xml_import';

// подключаюсь с PDO на БД
try {
    $dbConnection = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Failed to connect to the database: " . $e->getMessage());
}

$columns = array_keys((array)$xml->children()[0]);

$tableName = 'users';
$createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (id INT AUTO_INCREMENT PRIMARY KEY, ";

foreach ($columns as $column) {
    $createTableQuery .= "$column VARCHAR(255), ";
}

$createTableQuery = rtrim($createTableQuery, ', ') . ")";
try {
    $dbConnection->exec($createTableQuery);
} catch (PDOException $e) {
    die("Error creating table: " . $e->getMessage());
}

foreach ($xml->children() as $item) {
    $insertQuery = "INSERT INTO $tableName (";

    foreach ($columns as $column) {
        $insertQuery .= "$column, ";
    }

    $insertQuery = rtrim($insertQuery, ', ') . ") VALUES (";

    foreach ($columns as $column) {
        $insertQuery .= "'" . $dbConnection->quote((string)$item->$column) . "', ";
    }

    $insertQuery = rtrim($insertQuery, ', ') . ")";
    
    try {
        $dbConnection->exec($insertQuery);
    } catch (PDOException $e) {
        die("Error inserting data: " . $e->getMessage());
    }
}

// закроем конект
$dbConnection = null;

?>
