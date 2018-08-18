<?php
	//Connect to the concertapp database
	//Returns true if successful, false if failed
	function connectDB(){
		$server = 'localhost';
		$user = '';
		$pw = '';
		$db = '';
		
		$connection = new mysqli($server, $user, $pw, $db);
		var_dump($connection);
		if($connection->connect_error){
			die('Connection Failed: ' . $connection->connect_errno . '('
					.$connection->connect_error.')');
		}
		else return $connection;
		
		return false;
	}
	
	//Add venue to database and create appropriate tables
	//		-Add venue to the 'venues' table
	//		-Create event table for venue
	function createVenue($connection, $venueName){
		$query = "INSERT INTO venues (VenueName) VALUES (".$venueName.")";
		if(is_null($connection->query($query))){
			echo "Error adding ".$venueName.": ".$connection->error;
		}
		$query = "CREATE TABLE '".$venueName."_events' (
			EventID INT(11) AUTO_INCREMENT PRIMARY KEY,
			Details VARCHAR(750) NOT NULL UNIQUE KEY,
			Date	VARCHAR(20),
			Time	VARCHAR(20),
			Price	VARCHAR(5)
		)";
		$connection->query($query);
		if(is_null($connection->query($query))){
			echo "Error creating ".$venueName." event table: ".$connection->error;
		}
	}
	
	//Add events to appropriate venue event table
	function addEvents($connection, $venueName, $info){
		if($connection->query("SELECT 1 FROM venues WHERE VenueName = '".$venueName."' LIMIT 1") != FALSE){
			var_dump($connection);
			$stmt = $connection->prepare("INSERT INTO ".$venueName."_events (Details, Date, Time, Price) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $details, $date, $time, $price);
			
			foreach($info as $event){
				$details = $event["details"];
				$date = $event["date"];
				$time = $event["time"];
				$price = $price["price"];
				$stmt->execute();
			}
			
			$stmt->close();
		}
		else{
			echo $venueName." event table doesn't exist. Error: ".$connection->error."(".$connection->errno.")";
			createVenue($connection, $venueName);
			addEvents($connection, $venueName, $info);
		}
	}
?>