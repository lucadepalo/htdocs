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

	if(isTheseParametersAvailable(array('fk_linea', 'fk_posto'))){
		$fk_linea = $_POST['fk_linea'];
		$fk_posto = $_POST['fk_posto'];
		$stmt = $conn->prepare("SELECT fk_linea, fk_posto FROM dispone WHERE fk_linea = ? AND fk_posto = ?");
		$stmt->bind_param("ii", $fk_linea, $fk_posto);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Posto già occupato nella linea di irrigazione';
			$stmt->close();
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `dispone` (`fk_linea`, `fk_posto`) VALUES (?, ?)");
			$stmt->bind_param("ii", $fk_linea, $fk_posto);
		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Posto aggiunto correttamente alla linea di irrigazione';
				echo json_encode($response);

			}
			$stmt->close();
		}
	}
	
?>