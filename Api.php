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
					
					$stmt = $conn->prepare("SELECT pk_azienda FROM AZIENDA WHERE login = ? OR email = ?");
					$stmt->bind_param("ss", $username, $email);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'Utente già registrato';
						$stmt->close();
					}else{
						$stmt = $conn->prepare("INSERT INTO `AZIENDA` 
						(`pk_azienda`, `nome`, `login`, `pwd`, `lat`, `lon`, `cognome`, `email`, `emailVerificata`, `tipoUtente`, `sempreConnesso`) VALUES 
						('', ?, ?, ?, NULL, NULL, ?, ?, 'F', NULL, NULL)");
						$stmt->bind_param("sssss", $nome, $username, $password, $cognome, $email);
						
						if($stmt->execute()){
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
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'i parametri richiesti non sono disponibili'; 
				}
				
			break; 
			
			case 'login':
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
				}
			break; 

			case 'qrcode':
				if(isTheseParametersAvailable(array('qrSUT', 'qrAIRR'))){
					
					$qrSUT = $_POST['qrSUT'];
					$qrAIRR = $_POST['qrAIRR'];
					
					$stmt = $conn->prepare("SELECT fk_sensore, fk_attuatore FROM controlla WHERE fk_sensore = ? AND fk_attuatore = ?");
					$stmt->bind_param("ss", $qrSUT, $qrAIRR);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'Coppia sensore-attuatore già registrata';
						$stmt->close();
					} else {
						$stmt = $conn->prepare("INSERT INTO `controlla` (`fk_sensore`, `fk_attuatore`) VALUES (?, ?)");
						$stmt->bind_param("ss",$qrSUT, $qrAIRR);
					
						if($stmt->execute()){
							$response['error'] = false;
							$response['message'] = 'Coppia sensore-attuatore registrata con successo';
						}
					}
				}
			
			break;
			
			case 'suggest':
				if(isTheseParametersAvailable(array('fk_specie1'))){
					$fk_specie1 = $_POST['fk_specie1'];
					$stmt = $conn->prepare("SELECT SINERGIA.fk_specie2, SPECIE.nome FROM SINERGIA INNER JOIN SPECIE ON SINERGIA.fk_specie2 = SPECIE.pk_specie WHERE SINERGIA.fk_specie1 = ? AND SINERGIA.grado = 1");
					$stmt->bind_param("i", $fk_specie1);
					$stmt->execute();
					$speciesMap = array();
					$stmt->bind_result($fk_specie2, $nome);
					while ($stmt->fetch()) {
						$speciesMap[$fk_specie2] = $nome;
					}
					$response['error'] = false;
					$response['message'] = 'Synergic species retrieved successfully';
					$response['species'] = $speciesMap;
					echo json_encode($response);
				}
			break;

			case 'croplist':
				$stmt = $conn->prepare("SELECT pk_specie, nome FROM SPECIE");
				$stmt->execute();
				$result = $stmt->get_result();
				$speciesMap = array();
				while ($row = $result->fetch_assoc()) {
					$speciesMap[$row['pk_specie']] = $row['nome'];
				}
				$response['error'] = false;
				$response['message'] = 'Species retrieved successfully';
				$response['species'] = $speciesMap;
				echo json_encode($response);
			break;

			case 'irriga':
				if(isTheseParametersAvailable(array('fk_contenitore', 'fk_linea'))){
					$fk_contenitore = $_POST['fk_contenitore'];
					$fk_linea = $_POST['fk_linea'];
					$stmt = $conn->prepare("SELECT fk_contenitore, fk_linea FROM irriga WHERE fk_contenitore = ? AND fk_linea = ?");
					$stmt->bind_param("ii", $fk_contenitore, $fk_linea);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'Linea di irrigazione già presente nel contenitore';
						$stmt->close();
					} else {
						$stmt = $conn->prepare("INSERT INTO `irriga` (`fk_contenitore`, `fk_linea`) VALUES (?, ?)");
						$stmt->bind_param("ii", $fk_contenitore, $fk_linea);
					
						if($stmt->execute()){
							$response['error'] = false;
							$response['message'] = 'Linea di irrigazione aggiunta correttamente al contenitore';
						}
					}
				}
			break;

			case 'dispone':
				if(isTheseParametersAvailable(array('fk_linea', 'fk_posto'))){
					$fk_linea = $_POST['fk_linea'];
					$fk_posto = $_POST['fk_posto'];
					$stmt = $conn->prepare("SELECT fk_linea, fk_posto FROM dispone WHERE fk_linea = ? AND fk_posto = ?");
					$stmt->bind_param("ii", $fk_linea, $fk_posto);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'Posto già occupato nella linea di irrigazione';
						$stmt->close();
					} else {
						$stmt = $conn->prepare("INSERT INTO `dispone` (`fk_linea`, `fk_posto`) VALUES (?, ?)");
						$stmt->bind_param("ii", $fk_linea, $fk_posto);
					
						if($stmt->execute()){
							$response['error'] = false;
							$response['message'] = 'Posto aggiunto correttamente alla linea di irrigazione';
						}
					}
				}
			break;

			case 'assegnata':
				if(isTheseParametersAvailable(array('fk_posto', 'fk_specie'))){
					$fk_posto = $_POST['fk_posto'];
					$fk_specie = $_POST['fk_specie'];
					$stmt = $conn->prepare("SELECT fk_posto, fk_specie FROM assegnata WHERE fk_posto = ? AND fk_specie = ?");
					$stmt->bind_param("ii", $fk_posto, $fk_specie);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'Specie già piantata nel posto indicato';
						$stmt->close();
					} else {
						$stmt = $conn->prepare("INSERT INTO `assegnata`(`fk_ciclo_agrario`, `fk_posto`, `fk_specie`, `modo`) VALUES (NULL,?,?,NULL)");
						$stmt->bind_param("ii", $fk_posto, $fk_specie);
					
						if($stmt->execute()){
							$response['error'] = false;
							$response['message'] = 'Specie correttamente assegnata al posto indicato';
						}
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