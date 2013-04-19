<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$user = User::model()->find("id_user = ".$_SESSION['user_id']);

	// check old password
	echo $user->password."<br>";
	echo $_POST['current_password']."<br>";
	echo md5($_POST['current_password'])."<br>";
	if ($user->password == md5($_POST['current_password']))
	{
		$user->password = $_POST['new_password'];
		$user->confirm_password = $_POST['confirm_password'];
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
				Header("Location: index");
				// entah move ke index atau langsung login atau pesan sukses
			}
			else
			{
				// TODO print error screen
				echo "fail 1";
			}
		}
	}
	else
	{
		// TODO print error screen
		echo "fail 2";
	}
?>
