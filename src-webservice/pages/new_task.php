<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$task = new Task();
	$task->data = $_POST;
	$task->id_user = $this->currentUserId;
	
	// generate token
	$i = 0;
	$attachments = array();
	if (ISSET($_FILES['attachment']))
	{
		foreach($_FILES['attachment']['name'] as $name)
		{
			$temp = explode(".",$name);
			$extension = end($temp);
			$attachments[] = new Attachment();
			$attachments[$i]->attachment = strtoupper(md5(uniqid(rand(), true))).".".$extension;
			$attachments[$i]->temp_name = $_FILES['attachment']['tmp_name'][$i];
			$i++;
		}
	}
	$task->attachments = $attachments;
	
	$temperror = $task->checkValidity();
	
	if ($temperror)
	{
		// go to error page
		print_r($temperror);
	}
	else
	{
		if ($task->save())
		{
			Header("Location: tugas?id=".$task->id_task);
		}
		else
		{
			// go to error page
			echo "fail";
		}
	}
?>
