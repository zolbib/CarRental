<?php 
const DB_HOST = 'npv.h.filess.io:3307';
const DB_NAME = 'CarRental_breakupwet';
const DB_USERNAME = 'CarRental_breakupwet';
const DB_PASSWORD = '286b1fe700648dba84fd354bdca14c4a69a6cb0d';
$dbname = DB_NAME;
$dbhost = DB_HOST;
$dsn = "mysql:host=$dbhost;dbname=$dbname";

    $connection = new PDO($dsn,DB_USERNAME,DB_PASSWORD);
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 