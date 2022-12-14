<?php
	//skrypt dodajacy pliki do katalogu
	session_start();
	$user = $_SESSION['username']; //zalogowany uzytkownik
	
	if (file_exists($_FILES["uploaded_file"]["tmp_name"]))
	{
		$dbhost="";
		$connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname); //polaczenie z BD
		if (!$connection)
		{
			echo " MySQL Connection error." . PHP_EOL;
			echo "Errno: " . mysqli_connect_errno() . PHP_EOL;
			echo "Error: " . mysqli_connect_error() . PHP_EOL;
			exit;
		}
		
		//$datetime = date('Y-m-d H:i:s');
		
		$target_dir = 'films/'; //katalog zalogowanego uzytkownika
		
		$file_name = $_FILES["uploaded_file"]["name"];
		$file_extension = pathinfo($_FILES["uploaded_file"]["name"], PATHINFO_EXTENSION); //rozszerzenie pliku
		//kontrola rozszerzenia
		if($file_extension != 'mp4'){
			echo "Nieprawidlowe rozszerzenie pliku!<br><a href ='mynetflix.php'>Poprzednia strona</a><br />";
		}
		else{
			if(file_exists($_FILES['uploaded_file']['tmp_name'])){$file_target_location = $target_dir . $file_name;}
			else{$file_target_location = "";}
			move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $file_target_location);
			
			$matching_file = mysqli_query($connection, "SELECT * FROM film WHERE filename='$file_target_location'"); // wiersza, w którym login=login z formularza
			$matching_file_rekord = mysqli_fetch_array($matching_file); // wiersza z BD, struktura zmiennej jak w BD
			
			//id usera
			$user_id_query = mysqli_query($connection, "SELECT idu FROM user WHERE login='$user'");
			$user_id_array = mysqli_fetch_array($user_id_query);
			$user_id = $user_id_array[0];
			
			//dane piosenki
			$title = $_POST['title'];
			$director = $_POST['director'];
			$datetime = date('Y-m-d H:i:s');
			$filename = $file_target_location;
			$subtitles = $_POST['subtitles'];
			$film_type = $_POST['film_type'];
			
			if(!$matching_file_rekord){ //jezeli identyczny plik jeszcze nie istnieje
				$result = mysqli_query($connection, "INSERT INTO film (title, director, datetime, idu, filename, subtitles, idft) 
				VALUES ('$title', '$director', '$datetime', '$user_id', '$filename', '$subtitles', '$film_type');") or die ("DB error: $dbname");			
			}
			mysqli_close($connection);
		
			header('Location: mynetflix.php');	
		}
		
	}

?>