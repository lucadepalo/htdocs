<?php
	$servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "dbname";
	 
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	function isTheseParametersAvailable($params){
			
			foreach($params as $param){
				if(!isset($_GET[$param])){
					return false; 
				}
			}
			return true; 
		}
		
	if(isTheseParametersAvailable(array('pk', 'sensorData'))){
		
		$pk =$_GET['pk'];
		$sensorData =$_GET['sensorData'];
		$percentage = ((900-$sensorData)/6);
		$stmt = $conn->prepare("INSERT INTO `MISURA` (`pk_misura`, `valore`, `percentualeUmidita`, `unita_misura`, `dataMisura`, `fk_nodo_iot`) VALUES (NULL, ?, ?, NULL, current_timestamp(), ?)");
		$stmt->bind_param("ids", $sensorData, $percentage, $pk);
		
		if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Dati sensore inviati con successo';

			}
			$stmt->close();
			unset($stmt);
		}
		echo json_encode($response);
	
?>