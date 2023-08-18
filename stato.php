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

	if(isTheseParametersAvailable(array('fk_nodo_iot', 'priorita', 'valore'))){
		$fk_nodo_iot = $_POST['fk_nodo_iot'];
		$priorita = $_POST['priorita'];
		$valore = $_POST['valore'];
		$stmt = $conn->prepare("INSERT INTO `stato`(`pk_stato`, `data`, `percentuale`, `valore`, `fk_nodo_iot`, `priorita`) VALUES (NULL,NULL,NULL,?,?,?)");
		$stmt->bind_param("sss", $valore, $fk_nodo_iot, $priorita);
		$stmt->execute();
		$stmt->close();
		$response['error'] = false;
		$response['message'] = 'Stato registrato correttamente';
		echo json_encode($response);
	} else {
		$stmt->close();
		$response['error'] = true;
		$response['message'] = 'Errore: stato non registrato';
	}
	
?>