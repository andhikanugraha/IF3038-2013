Rp(function() 
{
	
	var profil_password = document.getElementById("edit_new_password");
	var profil_confirm_password = document.getElementById("edit_confirm_password");
	
	profil_password.onkeyup = function()
	{
		if (this.checkValidity())
		{
			this.className = "";
			document.getElementById("edit_confirm_password").pattern = this.value;
		}
		else
			this.className = "invalid";
		check_submit();
	}
	
	profil_confirm_password.onkeyup = function()
	{
		if (this.checkValidity() && profil_password.checkValidity())
			this.className = "";
		else		
			this.className = "invalid";
		check_submit();
	}
	
	function check_submit()
	{
		if ((profil_password.checkValidity()) && 
			(profil_confirm_password.checkValidity()))
		{
			document.getElementById("edit_pass_submit").disabled="";
		}
		else
		{
			document.getElementById("edit_pass_submit").disabled="disabled";
		}
	}
});
