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
	if(isTheseParametersAvailable(array('username', 'password'))){
		
		$username = $_POST['username'];
		$password = md5($_POST['password']); 
		
		$stmt = $conn->prepare("SELECT pk_azienda, login, email, nome, cognome FROM AZIENDA WHERE login = ? AND pwd = ?");
		$stmt->bind_param("ss",$username, $password);
		
		
		$stmt->execute();
		
		$stmt->store_result();
		
		if($stmt->num_rows > 0){
			
			$stmt->bind_result($id, $username, $email, $nome, $cognome);
			$stmt->fetch();
			
			$user = array(
				'id'=>$id, 
				'username'=>$username, 
				'email'=>$email,
				'nome'=>$nome,
				'cognome'=>$cognome
			);
			
			$response['error'] = false; 
			$response['message'] = 'Login effettuato'; 
			$response['user'] = $user; 
		}else{
			$response['error'] = false; 
			$response['message'] = 'Username o password non validi';
		}
		$stmt->close();
		echo json_encode($response);
	}
?>