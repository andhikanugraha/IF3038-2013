<?php
	$user = new User();
	$user->data = $_POST;
	$user->avatar = $_FILES['avatar']['name'];	
	$temperror = $user->checkValidity();
	
	if ($temperror)
	{
		// TODO print error screen
		$message = 'Registration failed. <a href="index.php">Try again</a>.';
		include '404.php';
	}
	else
	{
		// generate token
		$temp = explode(".",$_FILES['avatar']['name']);
		$extension = end($temp);
		$new_filename = strtoupper(md5(uniqid(rand(), true))).".".$extension;
		$user->avatar = $new_filename;
		if ($user->save())
		{
			move_uploaded_file($_FILES["avatar"]["tmp_name"],$_SESSION['full_path']."upload/user_profile_pict/" . $new_filename);
			
			$_SESSION['user_id'] = DBConnection::insertId();
			header("Location: dashboard.php");
			// entah move ke index atau langsung login atau pesan sukses
		}
		else
		{
			// TODO print error screen
			$message = 'Registration failed. <a href="index.php">Try again</a>.';
			include '404.php';
		}
	}
?>