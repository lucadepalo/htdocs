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

	if(isTheseParametersAvailable(array('pk', 'Type', 'SensorType', 'ValoreMinimo', 'ValoreMassimo'))){
		$pk =$_GET['pk'];
		$Type =$_GET['Type'];
		$SensorType=$_GET['SensorType'];
		$ValoreMinimo=$_GET['ValoreMinimo'];
		$ValoreMassimo=$_GET['ValoreMassimo'];

		$stmt = $conn->prepare("SELECT pk_nodo_iot FROM NODO_IOT WHERE pk_nodo_iot = ?");
		$stmt->bind_param("s", $pk);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'RegExist';
			$stmt->close();
			unset($stmt);
		} else {
			$stmt->close();
			unset($stmt);
			$stmt = $conn->prepare("INSERT INTO `NODO_IOT` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES (?, NULL, NULL, NULL, NULL, ?, ?, NULL, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL)");
			$stmt->bind_param("sssii", $pk, $Type, $SensorType, $ValoreMinimo, $ValoreMassimo);

			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'RegOK';

			} else {
				$response['error'] = true;
				$response['message'] = 'RegNot';
			}
			$stmt->close();
			unset($stmt);
		}
	echo json_encode($response);
	}
?>