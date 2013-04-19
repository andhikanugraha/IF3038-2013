var form_login = document.getElementById("login_form");
form_login.onsubmit = function(e)
{
	e.preventDefault();

	var data = serialize(form_login);
	var request = new ajaxRequest();
	request.onreadystatechange = function()
	{
		if (request.readyState==4)
		{
			if (request.status==200 || window.location.href.indexOf("http")==-1)
			{
				var result = JSON.parse(request.responseText);
				console.log(result);
				if (result.status == "success")
				{
					window.location = "dashboard.php";
				}
				else
				{
					alert("Username dan password yang dimasukkan salah.");
					document.getElementById("login_username").value = "";
					document.getElementById("login_password").value = "";
				}
			}
			else
			{
				alert("Mohon maaf, sedang ada kesalahan pada server.");
				document.getElementById("login_username").value = "";
				document.getElementById("login_password").value = "";
			}
		}
	}
	request.open("POST", "api/login", true);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.send(data);
	return false;
}