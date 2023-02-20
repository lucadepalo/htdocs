<?php

	// database config
	$servername = "localhost";
    $dbname = "cloud_simple";
    $username = "root";
    $password = "";
	$servicename =$_GET['nomeServizio'];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
	
		if ($servicename == "RegAct") {
		
		$pk =$_GET['pk'];
		$type =$_GET['type'];
		$ActType =$_GET['ActType'];
		//query attuatori
		$sql_regact = "INSERT INTO `nodo_iot` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES ('$pk', NULL, NULL, NULL, NULL, '$type', '$ActType', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE pk_nodo_iot = '$pk';";
		if ($conn->query($sql_regact) === TRUE) {
			echo "regOK";
			echo "Attuatore registrato con successo";
		} 
		else {
			echo "regNOT";
			echo "Error: " . $sql_regact . "<br>" . $conn->error;
		}
    } elseif ($servicename == "GetActState") {
		
		$pk =$_GET['pk'];
		$state =$_GET['state'];
		$ActType =$_GET['ActType'];
		
		$sql_getactstate = "INSERT INTO `stato` (`pk_stato`, `data`, `percentuale`,	`valore`, `fk_nodo_iot`, `priorita`) VALUES (NULL, current_timestamp(), NULL, '$state', '$pk', '0');";
		
		if ($conn->query($sql_getactstate) === TRUE) {
			if (random_int(0,1)==0) {
				echo "A";
			} else {
				echo "C";
			}
		} 
		else {
			echo "Error: " . $sql_getactstate . "<br>" . $conn->error;
		}
    } elseif ($servicename == "RegSensor") {
		
		$pk =$_GET['pk'];
		$type =$_GET['type'];
		$SensorType=$_GET['SensorType'];
		$ValoreMinimo=$_GET['ValoreMinimo'];
		$ValoreMassimo=$_GET['ValoreMassimo'];
		
		$sql_regsensor =
		"INSERT INTO `nodo_iot` (`pk_nodo_iot`, `nome`, `x`, `y`, `z`, `tipo`, `tipo_attuatore`, `tipo_sensore`, `valore_min`, `valore_max`, `statoCalcolato`, `p_min`, `p_max`, `fk_posto`, `fk_contenitore`, `icona`) VALUES ('$pk', NULL, NULL, NULL, NULL, '$type', '$SensorType', NULL, '$ValoreMinimo', '$ValoreMassimo', NULL, NULL, NULL, NULL, NULL, NULL)	ON DUPLICATE KEY UPDATE pk_nodo_iot = '$pk';";
		
		if ($conn->query($sql_regsensor) === TRUE) {
			echo "Sensore registrato con successo";
		} 
		else {
			echo "Error: " . $sql_regsensor . "<br>" . $conn->error;
		}
	} elseif ($servicename == "RegData") {
		$pk =$_GET['pk'];
		$sensorData =$_GET['sensorData'];
		$percentage = ((901-$sensorData)/6);
		$sql_regdata = "INSERT INTO `misura` (`pk_misura`, `valore`, `percentualeUmidita`, `unita_misura`, `dataMisura`, `fk_nodo_iot`) VALUES (NULL, '$sensorData', '$percentage', NULL, current_timestamp(), '$pk');";
		
		if ($conn->query($sql_regdata) === TRUE) {
			echo "Dati sensore inviati con successo";
		} 
		else {
			echo "Error: " . $sql_regdata . "<br>" . $conn->error;
		}
	}
?>