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

	function writeLog($message) {
    // Prepara il messaggio con il timestamp
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;

    // Scrive il log nel file debug.txt
    file_put_contents('debug.txt', $logMessage, FILE_APPEND);
}

	// Log di tutti i dati POST
	$postData = print_r($_POST, true);
	writeLog("Dati POST ricevuti:\n" . $postData);


	if (isTheseParametersAvailable(array('qrSUT', 'qrAIRR', 'ActType', 'SensorType', 'MinSUT', 'MaxSUT'))) {

		$qrSUT = $_POST['qrSUT'];   
		$qrAIRR = $_POST['qrAIRR'];
		
		$ActType = $_POST['ActType'];
		
		$SensorType = $_POST['SensorType'];
		$ValoreMinimo = $_POST['MinSUT'];
		$ValoreMassimo = $_POST['MaxSUT'];

		writeLog("(in iftheseparametersavailable:) Dati POST ricevuti:\n" . $postData);

		$stmt = $conn->prepare("SELECT pk_nodo_iot FROM nodo_iot WHERE pk_nodo_iot = ?");
		$stmt->bind_param("s", $qrAIRR);
		$stmt->execute();
		writeLog("Check AIRR in NODO_IOT:\n");
		
		if ($stmt->num_rows == 0) {
			$stmt->close();
			unset($stmt);
			$stmt = $conn->prepare("INSERT INTO `NODO_IOT` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES (?, NULL, NULL, NULL, NULL, 'attuatore', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE pk_nodo_iot = ?");
			$stmt->bind_param("sss", $qrAIRR, $ActType, $qrAIRR);
			$stmt->execute();
			$stmt->close();
			unset($stmt);
			writeLog("(after insert:)AIRR NOT in NODO_IOT:\n");
		} else {
			$response['error'] = true;
			$response['message'] = 'Attuatore già registrato nel nodo';
			$stmt->close();
			unset($stmt);
			writeLog("AIRR IS ALREADY PRESENT in NODO_IOT:\n");
		}
		
		$stmt = $conn->prepare("SELECT pk_nodo_iot FROM nodo_iot WHERE pk_nodo_iot = ?");
		$stmt->bind_param("s", $qrSUT);
		$stmt->execute();
		writeLog("Check SUT in NODO_IOT:\n");


		if ($stmt->num_rows == 0) {
			writeLog("(BEFORE insert:)SUT NOT in NODO_IOT:\n");
			$stmt->close();
			unset($stmt);
			$stmt = $conn->prepare("INSERT INTO `nodo_iot` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES (?, NULL, NULL, NULL, NULL, 'sensore', NULL, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE pk_nodo_iot = ?");
			$stmt->bind_param("ssiis", $qrSUT, $SensorType, $ValoreMinimo, $ValoreMassimo, $qrSUT);
			$stmt->execute();
			$stmt->close();
			unset($stmt);
			writeLog("(after insert:)SUT NOT in NODO_IOT:\n");
		} else {
			$response['error'] = true;
			$response['message'] = 'Sensore già registrato nel nodo';
			$stmt->close();
			unset($stmt);
			writeLog("SUT IS ALREADY PRESENT in NODO_IOT:\n");
		}
		
		$stmt = $conn->prepare("SELECT fk_sensore, fk_attuatore FROM controlla WHERE fk_sensore = ? AND fk_attuatore = ?");
		$stmt->bind_param("ss", $qrSUT, $qrAIRR);
		$stmt->execute();
		writeLog("Check AIRR-SUT in CONTROLLA:\n");

		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Coppia sensore-attuatore già registrata';
			$stmt->close();
			unset($stmt);
			writeLog("COUPLE IS ALREADY PRESENT in CONTROLLA:\n");
		} else {
			$stmt->close();
			unset($stmt);
			$stmt = $conn->prepare("INSERT INTO `controlla` (`fk_sensore`, `fk_attuatore`) VALUES (?, ?)");
			$stmt->bind_param("ss",$qrSUT, $qrAIRR);
			writeLog("(after insert:)COUPLE NOT in CONTROLLA:\n");

		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Coppia sensore-attuatore registrata con successo';
				writeLog("(after insert:)COUPLE SUCCESSFULLY INSERTED in CONTROLLA:\n");

			}
			$stmt->close();
			unset($stmt);
		}
		echo json_encode($response);
	}
?>