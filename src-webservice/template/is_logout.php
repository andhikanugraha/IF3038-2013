<?php	
	if (ISSET($_SESSION['user_id']))
	{
		Header("Location:".$_SESSION['full_url']."dashboard.php");
	}
?>