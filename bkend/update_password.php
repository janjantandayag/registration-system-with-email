<?php 	
	// Connect database
	include('connection.php');
	$connection = connectToDB();

	// Get email aaddress
	$data = json_decode(file_get_contents('php://input'), true);

	$token = $data['token'];
	$password = $data['password'];

	// Update reset link
	// Set token to null
	$password = password_hash($password, PASSWORD_DEFAULT);
	try {
		$sql = "UPDATE users SET password='$password', token='' WHERE token='$token'";
		$connection->exec($sql);	

		echo json_encode([
	  		'success' => true,
	  		'message' => 'Password successfully changed'
		]);		
	} catch (\Exception $e) {
		echo json_encode([
	  		'success' => false,
	  		'message' => 'Error: ' . $e->getMessage()
		]);		
	}
?>