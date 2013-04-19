if (localStorage.MOA_userList == null)
{
	// pendefinisian user default
	var user = new Object();
	user.userName = "admin";
	user.passWord = "admin123";
	user.fullName = "Administrator Jordan";
	user.birthDate = "20/11/1992";
	user.emailAddress = "fernandojordan.92@gmail.com";
	user.Avatar = "images/avatar.jpg";
	
	var listuser = new Array();
	listuser[0] = user;
	
	localStorage.MOA_userList = JSON.stringify(listuser);
}

if (localStorage.MOA_categoryList == null)
{
	var users = new Array();
	users[0] = "admin";
	
	var category = new Object();
	category.users = users;
	
	category.name = "Pemrograman Internet";	
	var listcategory = new Array();
	listcategory[0] = category;
	
	category = new Object();
	category.users = users;
	category.name = "Kriptografi";	
	listcategory[1] = category;
	
	category = new Object();
	category.users = users;
	category.name = "Metode Numerik";	
	listcategory[2] = category;
	
	localStorage.MOA_categoryList = JSON.stringify(listcategory);
}

if (localStorage.MOA_taskList == null)
{
	var task = new Object();
	task.category = 0;
	task.name = "Tubes 1";
	task.attachment = "task_files/sesuatu.jpg";
	task.deadline = "22/02/2013";
	
	var asignee = new Array();
	asignee[0] = "admin";
	task.asignee = asignee;
	
	var tag = new Array();
	tag[0] = "HTML";
	tag[1] = "CSS";
	tag[2] = "Javascript";
	
	task.tag = tag;
	
	var comment = new Object();
	comment.commentator = "admin";
	comment.talk = "tes dong";
	var comments = new Array();
	comments[0] = comment;
	
	task.comment = comments;
	
	var listtask = new Array();
	listtask[0] = task;
	
	localStorage.MOA_taskList = JSON.stringify(listtask);
}