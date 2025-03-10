<?php
$servername = 'mariadb'; // Container Name
$dbname = 'test';  // Database Name
$user ='root'; // Database ID
$password = 'root'; // Database Password
$port = '3306'; // DATABASE Port

try
{
    $connect = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $user, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo '<h1>Docker NGINX PHP-FPM MariaDB</h1>';
    echo 'Database Connection Successfully!';
}
catch(PDOException $ex)
{
    echo 'Connection Failed: ' . $ex->getMessage() . '<br>';
}
?>