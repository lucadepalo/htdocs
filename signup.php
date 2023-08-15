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
			('', ?, ?, ?, NULL, NULL, ?, ?, 'F', NULL, NULL)");
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
				
				$response['error'] = false; 
				$response['message'] = 'Registrazione avvenuta con successo'; 
				$response['user'] = $user; 
			}
		}
		echo json_encode($response);
	}else{
		$response['error'] = true; 
		$response['message'] = 'i parametri richiesti non sono disponibili'; 
		echo json_encode($response);
	}
?>