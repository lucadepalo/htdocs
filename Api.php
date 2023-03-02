<?php 

	require_once 'DbConnect.php';
	
	$response = array();
	
	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
			
			case 'signup':
				if(isTheseParametersAvailable(array('username','email','password','nome','cognome'))){
					$username = $_POST['username']; 
					$email = $_POST['email']; 
					$password = md5($_POST['password']);
					$nome = $_POST['nome']; 
					$cognome = $_POST['cognome']; 
					
					$stmt = $conn->prepare("SELECT pk_azienda FROM azienda WHERE login = ? OR email = ?");
					$stmt->bind_param("ss", $username, $email);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'User already registered';
						$stmt->close();
					}else{
						$stmt = $conn->prepare("INSERT INTO `azienda` 
						(`pk_azienda`, `nome`, `login`, `pwd`, `lat`, `lon`, `cognome`, `email`, `emailVerificata`, `tipoUtente`, `sempreConnesso`) VALUES 
						('', ?, ?, ?, NULL, NULL, ?, ?, 'F', NULL, NULL)");
						$stmt->bind_param("sssss", $nome, $username, $password, $cognome, $email);
						
						/*$stmt = $conn->prepare("INSERT INTO azienda (username, email, password, nome, cognome) VALUES (?, ?, ?, ?, ?)");
						$stmt->bind_param("sssss", $username, $email, $password, $nome, $cognome);*/



						if($stmt->execute()){
							$stmt = $conn->prepare("SELECT pk_azienda, login, email, nome, cognome FROM azienda WHERE login = ?"); 
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
							$response['message'] = 'User registered successfully'; 
							$response['user'] = $user; 
						}
					}
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break; 
			
			case 'login':
				if(isTheseParametersAvailable(array('username', 'password'))){
					
					$username = $_POST['username'];
					$password = md5($_POST['password']); 
					
					$stmt = $conn->prepare("SELECT pk_azienda, login, email, nome, cognome FROM azienda WHERE login = ? AND pwd = ?");
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
						$response['message'] = 'username successfull'; 
						$response['user'] = $user; 
					}else{
						$response['error'] = false; 
						$response['message'] = 'Invalid username or password';
					}
				}
			break; 
			
			default: 
				$response['error'] = true; 
				$response['message'] = 'Invalid Operation Called';
		}
		
	}else{
		$response['error'] = true; 
		$response['message'] = 'Invalid API Call';
	}
	
	echo json_encode($response);
	
	function isTheseParametersAvailable($params){
		
		foreach($params as $param){
			if(!isset($_POST[$param])){
				return false; 
			}
		}
		return true; 
	}