<?php

function connectToDB(){	
	$servername = "localhost";
	$username = "root";
	$password = "";

	try {
		$conn = new PDO("mysql:host=$servername;dbname=registrations", $username, $password);
		// Set pdo error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// echo 'connected successfully';
	} catch (Exception $e) {
		// echo 'failed' . $e->getMessage();	
	}

	return $conn;
}