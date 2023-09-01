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

	if (isTheseParametersAvailable(array('qrSUT', 'qrAIRR'))) {

		$qrSUT = $_POST['qrSUT'];   
		$qrAIRR = $_POST['qrAIRR'];

		$stmt = $conn->prepare("SELECT fk_sensore, fk_attuatore FROM controlla WHERE fk_sensore = ? AND fk_attuatore = ?");
		$stmt->bind_param("ss", $qrSUT, $qrAIRR);
		$stmt->execute();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Coppia sensore-attuatore già registrata';
			$stmt->close();
			unset($stmt);
		} else {
			$stmt->close();
			unset($stmt);
			$stmt = $conn->prepare("INSERT INTO `controlla` (`fk_sensore`, `fk_attuatore`) VALUES (?, ?)");
			$stmt->bind_param("ss",$qrSUT, $qrAIRR);

		
			if($stmt->execute()){
				$response['error'] = false;
				$response['message'] = 'Coppia sensore-attuatore registrata con successo';

			}
			$stmt->close();
			unset($stmt);
		}
		echo json_encode($response);
	}
?>