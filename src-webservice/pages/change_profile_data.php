<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$user = User::model()->find("id_user = ".$_SESSION['user_id']);
	
	// replace values
	foreach ($_POST as $key => $value)
	{
		$user->$key = $_POST[$key];
	}
	// create confirm password
	$user->confirm_password = $user->password;
	
	$temperror = $user->checkValidity();
	
	if ($temperror)
	{
		// TODO print error screen
		print_r($temperror);
	}
	else
	{
		if ($user->save())
		{
			if (ISSET($_FILES['avatar']))
			{
				// replace current avatar
				move_uploaded_file($_FILES["avatar"]["tmp_name"],$_SESSION['full_path']."upload/user_profile_pict/" . $user->avatar);
			}
			Header("Location: index");
			// entah move ke index atau langsung login atau pesan sukses
		}
		else
		{
			// TODO print error screen
			echo "fail";
		}
	}
?>
