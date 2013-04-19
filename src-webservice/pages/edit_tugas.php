<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$task = Task::model()->find("id_task = ".$_POST['id_task']);
	
	if ($task->getEditable($this->app->currentUserId))
	{
		// replace values
		foreach ($_POST as $key => $value)
		{
			$task->$key = $_POST[$key];
		}
		
		// generate token
		$i = 0;
		$attachments = array();
		if (ISSET($_FILES['attachment']))
		{
			foreach($_FILES['attachment']['name'] as $name)
			{
				if ($name!=null)
				{
					$temp = explode(".",$name);
					$extension = end($temp);
					$attachments[] = new Attachment();
					$attachments[$i]->attachment = strtoupper(md5(uniqid(rand(), true))).".".$extension;
					$attachments[$i]->temp_name = $_FILES['attachment']['tmp_name'][$i];
					$i++;
				}
			}
		}
		$task->attachments = $attachments;
		
		$temperror = $task->checkValidity();
		
		if ($temperror)
		{
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
				echo "fail";
			}
		}
	}
	else
	{
		// render error page
	}
?>
