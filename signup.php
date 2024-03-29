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

	if(isTheseParametersAvailable(array('username','email','password','nome','cognome'))){
		$username = $_POST['username']; 
		$email = $_POST['email']; 
		$password = md5($_POST['password']);
		$nome = $_POST['nome']; 
		$cognome = $_POST['cognome']; 
		
		$stmt = $conn->prepare("SELECT pk_azienda FROM AZIENDA WHERE login = ? OR email = ?");
		$stmt->bind_param("ss", $username, $email);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0){
			$response['error'] = true;
			$response['message'] = 'Utente già registrato';
			$stmt->close();
		}else{
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO `AZIENDA` 
			(`pk_azienda`, `nome`, `login`, `pwd`, `lat`, `lon`, `cognome`, `email`, `emailVerificata`, `tipoUtente`, `sempreConnesso`) VALUES 
			(NULL, ?, ?, ?, NULL, NULL, ?, ?, 'F', NULL, NULL)");
			
			$stmt->bind_param("sssss", $nome, $username, $password, $cognome, $email);
			
			if($stmt->execute()){
				$stmt->close();
				$stmt = $conn->prepare("SELECT pk_azienda, login, email, nome, cognome FROM AZIENDA WHERE login = ?"); 
				$stmt->bind_param("s",$username);
				$stmt->execute();
				$stmt->bind_result($id, $username, $email, $nome, $cognome);
				$stmt->fetch();
				
				$user = array(
					'id'=>$id, 
					'username'=>$username, 
					'email'=>$email,
					'nome'=>$nome,
					'cognome'=>$cognome
				);
				
				$stmt->close();

				$stmt = $conn->prepare("SELECT `fk_azienda` FROM `CONTENITORE` WHERE `fk_azienda` = ?");
				$stmt->bind_param("i", $id);
				$stmt->execute();
				$stmt->store_result();
		
				if($stmt->num_rows > 0){
					$response['error'] = true;
					$response['message'] = 'Contenitore già presente';
					$stmt->close();
				}else{
					$stmt->close();
					$stmt = $conn->prepare("INSERT INTO `CONTENITORE` (`pk_contenitore`, `numPosti`, `numContenitore`, `numLinee`, `fk_azienda`, `tipo_terreno`, `lat`, `lon`, `codice`, `modoFertirrigazione`, `lunghezza`, `larghezza`) VALUES ('1', '8', '1', '1', ?, 'medio', '0.00000000', '0.00000000', NULL, 'mm', NULL, NULL)");
					$stmt->bind_param("i", $id);
		
					if($stmt->execute()){
						$response['error'] = false;
						$response['message'] = 'Registrazione avvenuta con successo'; 
						$response['user'] = $user; 
					} else {
						$response['error'] = true;
						$response['message'] = 'Errore nella aggiunta del contenitore';
					}
			$stmt->close();
				}				
			}
		}
		echo json_encode($response);
	}else{
		$response['error'] = true; 
		$response['message'] = 'i parametri richiesti non sono disponibili'; 
		echo json_encode($response);
	}
?>
