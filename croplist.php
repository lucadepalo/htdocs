<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "cloud_simple";
	 
	$conn = new mysqli($servername, $username, $password, $database);
	 
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$stmt = $conn->prepare("SELECT pk_specie, nome FROM SPECIE");
	$stmt->execute();
	$result = $stmt->get_result();
	$speciesMap = array();
	while ($row = $result->fetch_assoc()) {
		$speciesMap[$row['pk_specie']] = $row['nome'];
	}
	$stmt->close();
	$response['error'] = false;
	$response['message'] = 'Species retrieved successfully';
	$response['species'] = $speciesMap;
	echo json_encode($response);
	
?>