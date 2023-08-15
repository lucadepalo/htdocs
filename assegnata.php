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

	if(isTheseParametersAvailable(array('fk_posto', 'fk_specie'))){
		$fk_posto = $_POST['fk_posto'];
		$fk_specie = $_POST['fk_specie'];
		$stmt = $conn->prepare("SELECT fk_posto, fk_specie FROM assegnata WHERE fk_posto = ? AND fk_specie = ?");
		$stmt->bind_param("ii", $fk_posto, $fk_specie);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Specie già piantata nel posto indicato';
			echo json_encode($response);
			$stmt->close();
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `assegnata`(`fk_ciclo_agrario`, `fk_posto`, `fk_specie`, `modo`) VALUES (NULL,?,?,NULL)");
			$stmt->bind_param("ii", $fk_posto, $fk_specie);
		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Specie correttamente assegnata al posto indicato';
				echo json_encode($response);
			}
			$stmt->close();
		}
	}
	
?>