var login_area = document.getElementById("login_area");
login_area.onmouseover = function()
{
	document.getElementById("login_link").className = "logout_link_focus";
	document.getElementById("login_button").className = "logout_button_focus";
}

login_area.onmouseout = function()
{
	document.getElementById("login_link").className = "";
	document.getElementById("login_button").className = "";
}

login_area.onclick = function()
{
	window.location = base_url+"api/logout";
}