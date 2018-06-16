<?php

	$run = true;
	while ($run) {
		echo "\n Choose what you want to do:
		a - add client;
		u - update client;
		d - delete client;
		s - save clients list in CSV;
		e - exit;\n Your choice: ";
		
		$choice = fopen ("php://stdin","r");
		$input = fgets($choice);
		
		switch (trim($input)) 
		{
			case 'a':
				addClient();
				break;
			case 'u':
				updateClient();
				break;
			case 'd':
				deleteClient();
				break;
			case 's':
				saveClientsToCSV ();
				break;
			case 'e':
				echo "You choose to exit. Have a good day.";
				$run=false;
				break;
			default:
			   echo "Your choice is not correct. Please choose again.";
			   break;
		}
	}

	function dbconnection() 
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "client_registration";
		
		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
			return null;
		}
		else return $conn;		
	}
	
	function addClient() 
	{
		$conn = dbconnection();
		if(is_null($conn)) exit;
		echo "\n---\n You choose add client. Write client's data (required):\n";
		
		do
		{
			echo "Client's firstname: ";
			$input = fopen ("php://stdin","r");
			$firstname = trim(fgets($input));
		}while(strlen($firstname)<1);
		
		do
		{
			echo "Client's lastname: ";
			$input = fopen ("php://stdin","r");
			$lastname = trim(fgets($input));
		}while(strlen($lastname)<1);
				
			echo "Client's email: ";
			$input = fopen ("php://stdin","r");
			$email = trim(fgets($input));
			$sql = "SELECT * FROM client WHERE email = '$email'";
			$result = $conn->query($sql);
			$rowcount=mysqli_num_rows($result); 
			while (!filter_var($email, FILTER_VALIDATE_EMAIL) OR $rowcount>0) 
			{
				echo "Email address '$email' is not valid or is used.\n Client's email: ";
				$input = fopen ("php://stdin","r");
				$email = trim(fgets($input));
				$sql = "SELECT * FROM client WHERE email = '$email'";
				$result = $conn->query($sql);
				$rowcount=mysqli_num_rows($result); 
			}		
		
		echo "Client's phonenumber1: ";
		$input = fopen ("php://stdin","r");
		$phonenumber1 = trim(fgets($input));
		$num_length = strlen((string)$phonenumber1);
		while (!($num_length == 9 && is_numeric($phonenumber1))) 
		{
			echo "Phonenumber1 - '$phonenumber1' is incorrect.\n Client's phonenumber1: ";
			$input = fopen ("php://stdin","r");
			$phonenumber1 = trim(fgets($input));
			$num_length = strlen((string)$phonenumber1);
		}
		
		
		echo "Client's phonenumber2 (or '-' for none): ";
		$input = fopen ("php://stdin","r");
		$phonenumber2 = trim(fgets($input));
		$num_length = strlen((string)$phonenumber2);
		while (!($num_length == 9 && is_numeric($phonenumber2))) 
		{
			if($phonenumber2=='-') 
			{$phonenumber2 = 'null'; break;}
			echo "phonenumber2 - '$num_length' is incorrect.\n Client's phonenumber2: ";
			$input = fopen ("php://stdin","r");
			$phonenumber2 = trim(fgets($input));
			$num_length = strlen((string)$phonenumber2);
		}		
		
		echo "Comment: ";
		$input = fopen ("php://stdin","r");
		$comment = trim(fgets($input));
				
		$sql = "INSERT INTO client (firstname, lastname, email, phonenumber1, phonenumber2, comment)
				VALUES ('$firstname','$lastname','$email',$phonenumber1, $phonenumber2,'$comment')";

		if ($conn->query($sql) === TRUE) {
			echo "\n---\nNew client's data saved successfully.\n---\n";
		} else {
			echo "\n---\nError: " . $sql . "\n" . $conn->error;
		}

		$conn->close();
	}
	
	function updateClient() 
	{
		$conn = dbconnection();
		if(is_null($conn)) exit;
		
		echo "Change client's data. Client's email: ";
		
		$input = fopen ("php://stdin","r");
		$email = trim(fgets($input));
		while (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			echo "Email address '$email' is not valid.\n Client's email: ";
			$input = fopen ("php://stdin","r");
			$email = trim(fgets($input));
		}
		
		$sql = "SELECT * FROM `client` WHERE `email`= '$email'";
		$result = $conn->query($sql);
		$rowcount=mysqli_num_rows($result);
		if ($rowcount == 0) {
			echo "There is no client with this email.";
		} 
		else 
		{
				foreach($result as $row) 
				{
					echo "Client's data:\n";
					echo " firstname: ".$row['firstname']."\n lastname: ".$row['lastname']."\n email: ".$row['email']."\n phonenumber1: ".$row['phonenumber1']."\n phonenumber2: ".$row['phonenumber2']."\n comment: ".$row['comment']."\n";
					$c_id = $row['c_id'];
				}				
				$updateData = " ";
				
				do{
					echo "Client's new firstname (or '-' if do not change): ";
						$input = fopen ("php://stdin","r");
						$firstname = trim(fgets($input));
				}while(strlen($firstname)<1);		
					if($firstname != '-'){$updateData=$updateData."firstname = '$firstname', ";}
				
				do{	
					echo "Client's new lastname (or '-' if do not change): ";
						$input = fopen ("php://stdin","r");
						$lastname = trim(fgets($input));
				}while(strlen($firstname)<1);		
					if($lastname != '-'){$updateData=$updateData."lastname = '$lastname', ";}
					
				echo "Client's new email (or '-' if do not change): ";
						$input = fopen ("php://stdin","r");
						$email = trim(fgets($input));						
					if($email != '-')
					{
						$sql = "SELECT * FROM client WHERE email = '$email'";
						$result = $conn->query($sql);
						$rowcount=mysqli_num_rows($result); 
						while (!filter_var($email, FILTER_VALIDATE_EMAIL) OR $rowcount>0) 
						{
							echo "Email address '$email' is not valid or is used.\n Repeat client's new email: ";
							$input = fopen ("php://stdin","r");
							$email = trim(fgets($input));
							$sql = "SELECT * FROM client WHERE email = '$email'";
							$result = $conn->query($sql);
							$rowcount=mysqli_num_rows($result); 
						}
						$updateData=$updateData."email = '$email', ";
					}
					
				echo "Client's new phonenumber1 (or '-' if do not change): ";
						$input = fopen ("php://stdin","r");
						$phonenumber1 = trim(fgets($input));
					if($phonenumber1 != '-')
					{
						$num_length = strlen((string)$phonenumber1);
						while (!($num_length == 9 && is_numeric($phonenumber1))) 
						{
							echo "Phonenumber1 - '$phonenumber1' is incorrect.\n Repeat client's new phonenumber1: ";
							$input = fopen ("php://stdin","r");
							$phonenumber1 = trim(fgets($input));
							$num_length = strlen((string)$phonenumber1);
						}
						$updateData=$updateData."phonenumber1 = '$phonenumber1', ";
					}
					
				echo "Client's new phonenumber2 (or '-' if do not change): ";
						$input = fopen ("php://stdin","r");
						$phonenumber2 = trim(fgets($input));
					if($phonenumber2 != '-')
					{
						$num_length = strlen((string)$phonenumber2);
						while (!($num_length == 9 && is_numeric($phonenumber2))) 
						{
							echo "Phonenumber2 - '$phonenumber2' is incorrect.\n Repeat client's new phonenumber2: ";
							$input = fopen ("php://stdin","r");
							$phonenumber2 = trim(fgets($input));
							$num_length = strlen((string)$phonenumber2);
						}
						$updateData=$updateData."phonenumber2 = '$phonenumber2', ";
					}
					
					
				echo "Client's new comment (or '-' if do not change): ";
						$input = fopen ("php://stdin","r");
						$comment = trim(fgets($input));
					if($comment != '-'){$updateData=$updateData."comment = '$comment', ";}	
					
				if(strlen($updateData)>10){
					$updateData = substr($updateData, 0, -2);	
					$sql = "UPDATE client SET $updateData where c_id=$c_id";
					if ($conn->query($sql) === TRUE) {
						echo "\n---\nClient's data changed successfully.\n---\n";
					} else {
						echo "\n---\nError: " . $sql . "\n" . $conn->error;
					}
				}
				else echo "There is nothing to change.";
		}
		
		$conn->close();
	}
	
	function deleteClient()
	{
		$conn = dbconnection();
		if(is_null($conn)) exit;
		
		echo "Delete client's data. Client's email: ";
		
		$input = fopen ("php://stdin","r");
		$email = trim(fgets($input));
		while (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			echo "Email address '$email' is not valid.\n Client's email: ";
			$input = fopen ("php://stdin","r");
			$email = trim(fgets($input));
		}
		
		$sql = "DELETE FROM client WHERE email = '$email'";
		if ($conn->query($sql) === TRUE) {
			echo "\n---\nClient's data was deleted successfully.\n---\n";
		} else {
			echo "\n---\nError: " . $sql . "\n" . $conn->error;
		}

		$conn->close();
	}
	
	function saveClientsToCSV ()
	{
		$file = fopen("Clients_list.csv","w");
		fwrite($file,"firstname;lastname;email;phonenumber1;phonenumber2;comment\n");
		$conn = dbconnection();
		if(is_null($conn)) exit;
		
		$sql = "SELECT * FROM client";
		$result = $conn->query($sql);
		foreach($result as $row) 
			{
				$line =  $row['firstname'].";".$row['lastname'].";".$row['email'].";".$row['phonenumber1'].";".$row['phonenumber2'].";".$row['comment']."\n";
				fwrite($file,$line);
			}
		fclose($file);
		$conn->close();
		echo "File was saved successfully.";
	}
?>

