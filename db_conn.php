<?php 
const DB_HOST = 'localhost';
const DB_NAME = 'carrental';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';
$dbname = DB_NAME;
$dbhost = DB_HOST;
$dsn = "mysql:host=$dbhost;dbname=$dbname";

    $connection = new PDO($dsn,DB_USERNAME,DB_PASSWORD);
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 