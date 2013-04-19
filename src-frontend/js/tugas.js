var Day = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
var Mon = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Des"];

Rp(function() 
{
	var delete_task = document.getElementById("removeTaskLink");
	if (delete_task != undefined) delete_task.onclick = function()
	{
		var serialized = "task_id="+id_task;
		var req = Rp.ajaxRequest('api/delete_task');
		req.onreadystatechange = function() {
			switch (req.readyState) {
				case 1:
				case 2:
				case 3:
					break;
				case 4:
					try {
						response = Rp.parseJSON(req.responseText);
						if (response.success)
						{
							window.location = "dashboard";
						}
					}
					catch (e) {

					}
					break;
			}
		}
		req.post(serialized);
		return false;
	}
	
	window.onload = function()
	{
		datePicker.init(document.getElementById("calendar"), document.getElementById("new_tugas"), "deadline");
	}
});

var asignee = document.getElementById("assignee");
asignee.setAttribute('autocomplete', 'off');
asignee.onkeyup = function()
{
	var value = asignee.value;
	var req = Rp.ajaxRequest();
	req.onreadystatechange = function() {
		switch (req.readyState) {
			case 1:
			case 2:
			case 3:
				Rp('#assignee').addClass('loading');
				break;
			case 4:
				Rp('#assignee').removeClass('loading');
				try {
					response = Rp.parseJSON(req.responseText);
					var elm = document.getElementById("auto_comp_assignee");
					var inflate = document.getElementById("auto_comp_inflate_assignee");
					inflate.innerHTML = "";
					var temp = 0;
					for (var i in response)
					{
						temp++;
						var newLi = document.createElement("li");
						newLi.innerHTML = "<a href='javascript:choose_assignee(\""+response[i].data.username+"\")'>"+
											response[i].data.username+"</a>";
						inflate.insertBefore(newLi, inflate.firstChild);
					}
					
					if (temp!=0)
						elm.style.display = "block";
				}
				catch (e) {

				}
				break;
		}
	}
	req.get('api/get_username?username=' + value);
}

function choose_assignee(username)
{
	var elm = document.getElementById("auto_comp_assignee");
	var value = asignee.value;
	value = value.substr(0, value.lastIndexOf(",")+1);
	value += username;
	asignee.value = value;
	elm.style.display = "none";
}

var tag = document.getElementById("tag");
tag.onkeyup = function()
{
	var value = tag.value;
	var req = Rp.ajaxRequest();
	req.onreadystatechange = function() {
		switch (req.readyState) {
			case 1:
			case 2:
			case 3:
				Rp('#tag').addClass('loading');
				break;
			case 4:
				Rp('#tag').removeClass('loading');
				try {
					response = Rp.parseJSON(req.responseText);
					var elm = document.getElementById("auto_comp_tag");
					var inflate = document.getElementById("auto_comp_inflate_tag");
					inflate.innerHTML = "";
					var temp = 0;
					for (var i in response)
					{
						temp ++;
						var newLi = document.createElement("li");
						newLi.innerHTML = "<a href='javascript:choose_tag(\""+response[i].data.tag_name+"\")'>"+
											response[i].data.tag_name+"</a>";
						inflate.insertBefore(newLi, inflate.firstChild);
					}
					
					if (temp!=0)
						elm.style.display = "block";
				}
				catch (e) {

				}
				break;
		}
	}
	req.get('api/get_tag?tag=' + value);
}

function choose_tag(temptag)
{
	var elm = document.getElementById("auto_comp_tag");
	var value = tag.value;
	value = value.substr(0, value.lastIndexOf(",")+1);
	value += temptag;
	tag.value = value;
	elm.style.display = "none";
}

// checkbox

handleTaskCheckbox = function(e) {
	Rp('.task-checkbox input[data-task-id]').prop('disabled', true);
	taskID = this.getAttribute('data-task-id');
	checked = this.checked;
	mark = Rp.ajaxRequest('api/mark_task')
	.complete(function() {
		Rp('.task-checkbox input[data-task-id]').prop('disabled', false);
		response = this.responseJSON();
		console.log(response.success);
		if (response.success) {
			Rp('.task-checkbox input[data-task-id]').prop('checked', response.done);
		}
		else {
			console.log('Failure to update status of task.');
		}
	})
	.post({
		'taskID': taskID,
		'completed': checked
	});
}

Rp('.task-checkbox input[data-task-id]').on('change', handleTaskCheckbox);
