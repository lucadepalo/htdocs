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
		
		if($priorita = 0){  
			$stmt = $conn->prepare("SELECT s.pk_specie, s.UT_min, s.UT_max FROM specie AS s JOIN assegnata AS a ON s.pk_specie = a.fk_specie");
			$stmt->execute();
			$result = $stmt->get_result();
			$resultMap = array();
			while ($row = $result->fetch_assoc()) {
				//riempire il map con le UT_min e UT_max
			}
			$stmt->close();	
			//fare la media degli UT_min e salvarla
			//fare la media degli UT_max e salvarla
			//fare query per restituire "percentualeUmidita" dalla tabella "misura" scegliendo solo la tupla immessa più di recente
			//fare un if-else: se la percentualeUmidita è maggiore o uguale alla media di UT_max impostare $valore=C, altrimenti impostare $valore=A
		}
		
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