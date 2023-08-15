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

	if(isTheseParametersAvailable(array('fk_contenitore', 'fk_linea'))){
		$fk_contenitore = $_POST['fk_contenitore'];
		$fk_linea = $_POST['fk_linea'];
		$stmt = $conn->prepare("SELECT fk_contenitore, fk_linea FROM irriga WHERE fk_contenitore = ? AND fk_linea = ?");
		$stmt->bind_param("ii", $fk_contenitore, $fk_linea);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Linea di irrigazione già presente nel contenitore';
			echo json_encode($response);
			$stmt->close();
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `irriga` (`fk_contenitore`, `fk_linea`) VALUES (?, ?)");
			$stmt->bind_param("ii", $fk_contenitore, $fk_linea);
		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Linea di irrigazione aggiunta correttamente al contenitore';
				echo json_encode($response);
			}
			$stmt->close();
		}
	}
	
?>