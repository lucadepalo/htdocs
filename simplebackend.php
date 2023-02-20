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
		$sql_regact = 
		"INSERT INTO `nodo_iot` (
		`pk_nodo_iot`,
		`nome`,
		`x`,
		`y`,
		`z`,
		`tipo`,
		`tipo_attuatore`,
		`tipo_sensore`,
		`valore_min`,
		`valore_max`,
		`statoCalcolato`,
		`p_min`,
		`p_max`,
		`fk_posto`,
		`fk_contenitore`,
		`icona`)
		
		VALUES (
		'$pk',
		NULL,
		NULL,
		NULL,
		NULL,
		'$type',
		'$ActType',
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL
		) ON DUPLICATE KEY UPDATE pk_nodo_iot = '$pk';";
		if ($conn->query($sql_regact) === TRUE) {
			echo "Attuatore registrato con successo";
		} 
		else {
			echo "Error: " . $sql_regact . "<br>" . $conn->error;
		}
    } elseif ($servicename == "GetActState") {
		
		//"GET /simplebackend.php?pk="+String(nodePk_AIRR)+"&nomeServizio=GetActState&state="+String(nodeCurrentState)+"&ActType="+nodeType_AIRR+" HTTP/1.1"
		
		$pk =$_GET['pk'];
		$state =$_GET['state'];
		$ActType =$_GET['ActType'];
		
		$sql_getactstate = "INSERT INTO `stato`
		(
		`pk_stato`, 
		`data`, 
		`percentuale`, 
		`valore`, 
		`fk_nodo_iot`, 
		`priorita`
		) 
		VALUES 
		(
		NULL, 
		current_timestamp(), 
		NULL, 
		'$state', 
		'$pk', 
		'0'
		);";
		
		//$sql_readstate = "SELECT
		
		
		if ($conn->query($sql_getactstate) === TRUE) {
			echo "Lettura stato attuatore";
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
		"INSERT INTO `nodo_iot` (
		`pk_nodo_iot`,
		`nome`,
		`x`,
		`y`,
		`z`,
		`tipo`,
		`tipo_attuatore`,
		`tipo_sensore`,
		`valore_min`,
		`valore_max`,
		`statoCalcolato`,
		`p_min`,
		`p_max`,
		`fk_posto`,
		`fk_contenitore`,
		`icona`)
		
		VALUES
		(
		'$pk',
		NULL,
		NULL,
		NULL,
		NULL,
		'$type',
		'$SensorType',
		NULL,
		'$ValoreMinimo',
		'$ValoreMassimo',
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL
		)
		ON DUPLICATE KEY UPDATE pk_nodo_iot = '$pk';";
		
		if ($conn->query($sql_regsensor) === TRUE) {
			echo "Sensore registrato con successo";
		} 
		else {
			echo "Error: " . $sql_regsensor . "<br>" . $conn->error;
		}
	} elseif ($servicename == "RegData") {
		
		//GET /simplebackend.php?pk="+String(nodePk_SUT)+"&nomeServizio=RegData&sensorData="+String(sensorValue)+" HTTP/1.1
		$pk =$_GET['pk'];
		$sensorData =$_GET['sensorData'];
		$percentage = ((901-$sensorData)/6);
		//$percentage =$_GET['percentage'];
		$sql_regdata =
		"INSERT INTO `misura`
		(
		`pk_misura`, 
		`valore`, 
		`percentualeUmidita`, 
		`unita_misura`, 
		`dataMisura`, 
		`fk_nodo_iot`
		) 
		VALUES 
		(
		NULL, 
		'$sensorData', 
		'$percentage', 
		NULL, 
		current_timestamp(), 
		'$pk'
		);";
		//ON DUPLICATE KEY UPDATE pk_nodo_iot = '$pk';";
		
		if ($conn->query($sql_regdata) === TRUE) {
			echo "Dati sensore inviati con successo";
		} 
		else {
			echo "Error: " . $sql_regdata . "<br>" . $conn->error;
		}
	}
?>