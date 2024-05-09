<?php 
const DB_HOST = 'sql11.freemysqlhosting.net';
const DB_NAME = 'sql11704516';
const DB_USERNAME = 'sql11704516';
const DB_PASSWORD = 'GkazGlCtxv';
$dbname = DB_NAME;
$dbhost = DB_HOST;
$dsn = "mysql:host=$dbhost;dbname=$dbname";

    $connection = new PDO($dsn,DB_USERNAME,DB_PASSWORD);
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 