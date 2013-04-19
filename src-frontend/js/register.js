/*----- Bagian Register ----*/
var register_form = document.getElementById("register_form");

var register_username = document.getElementById("register_username");
var register_password = document.getElementById("register_password");
var register_confirm_password = document.getElementById("register_confirm_password");
var register_name = document.getElementById("register_name");
var birth_date = document.getElementById("birth_date");
var register_email = document.getElementById("register_email");
var register_avatar = document.getElementById("register_avatar");

register_form.onsubmit = function()
{		
	if ((register_password.value==register_confirm_password.value) && (register_username.value!=register_password.value) &&
		(register_email.value!=register_password.value))
	{
		var status = false;
		var data = serialize(register_form);
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
						status = true;
					}
					else
					{
						for (var temp in result.error)
						{
							alert(result.error[temp]);
						}
					}
				}
				else
				{
					alert("Mohon maaf, sedang ada kesalahan pada server.");
				}
			}
		}
		request.open("POST", "api/register_check", false);
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.send(data);
		
		return status;
	}
	else if (register_password.value!=register_confirm_password.value)
	{
		alert("Konfirmasi sandi harus sama dengan sandi");
		return false;
	}
	else if (register_email.value==register_password.value)
	{
		alert("Alamat email tidak boleh sama dengan sandi");
		return false;
	}
	else if (register_username.value==register_password.value)
	{
		alert("Username tidak boleh sama dengan sandi");
		return false;
	}	
}

// added client side verification

register_username.onkeyup = function()
{
	if (this.checkValidity() && (register_username.value != register_password.value))
		this.className = "";
	else
		this.className = "invalid";
	check_submit();
}

register_password.onkeyup = function()
{
	if (this.checkValidity() && (register_username.value != register_password.value) && (register_email.value != register_password.value))
	{
		this.className = "";
		document.getElementById("register_confirm_password").pattern = this.value;
	}
	else
		this.className = "invalid";
	check_submit();
}

register_confirm_password.onkeyup = function()
{
	if (this.checkValidity() && register_password.checkValidity())
		this.className = "";
	else	
		this.className = "invalid";
	check_submit();
}

register_name.onkeyup = function()
{
	if (this.checkValidity())
		this.className = "";
	else
		this.className = "invalid";
	check_submit();
}

birth_date.onkeyup = function()
{
	if ((this.checkValidity()) && (check_date(this.value)))
		this.className = "";
	else
		this.className = "invalid";
	check_submit();
}

register_email.onkeyup = function()
{
	if (this.checkValidity() && (register_email.value != register_password.value))
		this.className = "";
	else
		this.className = "invalid";
	check_submit();
}

register_avatar.onchange = function()
{
	if (check_file_jpeg(this))
		this.className = "";
	else
		this.className = "invalid";
	check_submit();
}

function check_date(date)
{
	var temp = date.split("-");
	var d = new Date(parseInt(temp[0]), (parseInt(temp[1]) - 1), parseInt(temp[2]));
	if ((d) && ((d.getMonth() + 1) == parseInt(temp[1])) && (d.getDate() == Number(parseInt(temp[2]))) && (parseInt(temp[0]) >= 1955))
	{
		datePicker.populateTable(d.getMonth(),d.getFullYear());
		return true;
	}
	else
		return false;
}

function check_file_jpeg(image)
{
	return (image.value.match("^.+\.(jpe?g|JPE?G)$"));
}

function check_submit()
{
	if ((register_username.checkValidity()) && (register_password.checkValidity()) && 
		(register_confirm_password.checkValidity()) && (register_name.checkValidity()) && 
		(birth_date.checkValidity()) && (register_email.checkValidity()) && 
		(check_file_jpeg(register_avatar)) && (register_email.value != register_password.value) &&
		(register_email.value != register_password.value) && (check_date(birth_date.value)))
	{
		document.getElementById("register_submit").disabled="";
	}
	else
	{
		document.getElementById("register_submit").disabled="disabled";
	}
}
