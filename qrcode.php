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

	if (isTheseParametersAvailable(array('qrSUT', 'qrAIRR', 'ActType', 'SensorType', 'MinSUT', 'MaxSUT'))) {

		$qrSUT = $_POST['qrSUT'];   
		$qrAIRR = $_POST['qrAIRR'];
		
		$ActType = $_POST['ActType'];
		
		$SensorType = $_POST['SensorType'];
		$ValoreMinimo = $_POST['MinSUT'];
		$ValoreMassimo = $_POST['MaxSUT'];


		$stmt = $conn->prepare("SELECT pk_nodo_iot FROM nodo_iot WHERE pk_nodo_iot = ?");
		$stmt->bind_param("s", $qrAIRR);
		$stmt->execute();

		if ($stmt->num_rows == 0) {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `NODO_IOT` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES (?, NULL, NULL, NULL, NULL, 'attuatore', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE pk_nodo_iot = ?");
			$stmt->bind_param("sss", $qrAIRR, $ActType, $qrAIRR);
			$stmt->execute();
			$stmt->close();
		} else {
			$stmt->close();
		}
		
		$stmt = $conn->prepare("SELECT pk_nodo_iot FROM nodo_iot WHERE pk_nodo_iot = ?");
		$stmt->bind_param("s", $qrSUT);
		$stmt->execute();

		if ($stmt->num_rows == 0) {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `NODO_IOT` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES (?, NULL, NULL, NULL, NULL, 'sensore', NULL, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE pk_nodo_iot = ?");
			$stmt->bind_param("ssss", $qrSUT, $SensorType, $ValoreMinimo, $ValoreMassimo, $qrSUT);
			$stmt->execute();
			$stmt->close();
		} else {
			$stmt->close();
		}
		
		$stmt = $conn->prepare("SELECT fk_sensore, fk_attuatore FROM controlla WHERE fk_sensore = ? AND fk_attuatore = ?");
		$stmt->bind_param("ss", $qrSUT, $qrAIRR);
		$stmt->execute();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Coppia sensore-attuatore già registrata';
			$stmt->close();
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `controlla` (`fk_sensore`, `fk_attuatore`) VALUES (?, ?)");
			$stmt->bind_param("ss",$qrSUT, $qrAIRR);
		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Coppia sensore-attuatore registrata con successo';
			}
			$stmt->close();
		}
		echo json_encode($response);
	}
?>