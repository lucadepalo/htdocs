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

	if(isTheseParametersAvailable(array('pk_posto', 'numForo', 'fk_linea'))){
	
		$pk_posto = $_POST['pk_posto'];
		$numForo = $_POST['numForo'];
		$fk_linea = $_POST['fk_linea'];

		$stmt = $conn->prepare("SELECT pk_posto, numForo, fk_linea FROM dispone WHERE fk_linea = ? AND fk_posto = ? AND numForo = ?");
		$stmt->bind_param("iii", $fk_linea, $pk_posto, $numForo);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Posto già occupato nella linea di irrigazione';
			$stmt->close();
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `POSTO`(`pk_posto`, `numForo`, `fk_linea`) VALUES (?, ?, ?)");
			$stmt->bind_param("iii", $pk_posto, $numForo, $fk_linea);
		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Posto aggiunto correttamente alla linea di irrigazione';

			} else {
				$response['error'] = true;
				$response['message'] = 'Errore nella aggiunta del posto';
			}
			$stmt->close();
		}
	} else {
		$response['error'] = true;
		$response['message'] = 'Errore: parametri non disponibili';
	}
	echo json_encode($response);
	
?>
