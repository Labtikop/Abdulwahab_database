<?php
$host='localhost'; $user='root'; $pass=''; $dbname='online_bookstore';
$conn = mysqli_connect($host,$user,$pass,$dbname);
if(!$conn) die('DB error: '.mysqli_connect_error());
?>