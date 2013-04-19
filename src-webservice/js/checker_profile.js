Rp(function() {	
	var profil_name = document.getElementById("edit_name");
	var birth_date = document.getElementById("birth_date");
	var profil_email = document.getElementById("edit_email");
	var profil_avatar = document.getElementById("edit_avatar");
	
	profil_name.onkeyup = function()
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
	
	profil_email.onkeyup = function()
	{
		if ((this.checkValidity()))
			this.className = "";
		else
			this.className = "invalid";
		check_submit();
	}
	
	profil_avatar.onchange = function()
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
		if ((profil_name.checkValidity()) && (birth_date.checkValidity()) && 
			(profil_email.checkValidity()) && ((profil_avatar.value=="")||(check_file_jpeg(profil_avatar))) &&
			(check_date(birth_date.value)))
		{
			document.getElementById("edit_submit").disabled="";
		}
		else
		{
			document.getElementById("edit_submit").disabled="disabled";
		}
	}
	
	window.onload = function() 
	{
		datePicker.init(document.getElementById("calendar"), document.getElementById("edit_form"), "birth_date");
	}
});
