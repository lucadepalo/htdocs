<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "cloud_simple";
	 
	$conn = new mysqli($servername, $username, $password, $database);
	 
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	function isTheseParametersAvailable($params){
			
			foreach($params as $param){
				if(!isset($_POST[$param])){
					return false; 
				}
			}
			return true; 
		}

	if(isTheseParametersAvailable(array('fk_specie1'))){
		$fk_specie1 = $_POST['fk_specie1'];
		$stmt = $conn->prepare("SELECT SINERGIA.fk_specie2, SPECIE.nome FROM SINERGIA INNER JOIN SPECIE ON SINERGIA.fk_specie2 = SPECIE.pk_specie WHERE SINERGIA.fk_specie1 = ? AND SINERGIA.grado = 1");
		$stmt->bind_param("i", $fk_specie1);
		$stmt->execute();
		$speciesMap = array();
		$stmt->bind_result($fk_specie2, $nome);
		while ($stmt->fetch()) {
			$speciesMap[$fk_specie2] = $nome;
		}
		$stmt->close();
		$response['error'] = false;
		$response['message'] = 'Synergic species retrieved successfully';
		$response['species'] = $speciesMap;
		echo json_encode($response);
	}
	
?>