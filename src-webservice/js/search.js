var isLoading = {'user': false, 'task': false, 'category': false};

Rp(function() {
	window.loading = false;

	var pageCount = {
		'user': 1,
		'task': 1,
		'category': 1
	};

	getScrollTop = function() {
		return (window.pageYOffset !== undefined) ?
			window.pageYOffset :
			(document.documentElement || document.body.parentNode || document.body).scrollTop;
	}

	loadPartial = function(typ) {
		if (isLoading[typ])
			return;

		isLoading[typ] = true;

		page = pageCount[typ] + 1;
		var req = Rp.ajaxRequest('search_partial.php?q=' + q+ '&type=' + typ + '&page=' + page);
		req.onreadystatechange = function() {
			console.log(typ);
			list = document.getElementById(typ + 'List');
			if (req.readyState == 4) {
				if (req.responseText) {
					pageCount[typ]++;
					Rp('#' + type + 'List > p').css('display', 'none');
					list.innerHTML += req.responseText;
				}
				list.className = '';
				isLoading[typ] = false;
			}
			else {
				list.className = 'loading';
			}
		}
		loading = true;
		req.post();
	}

	var lastScroll;

	window.onscroll = function(e) {
		// Only when scrolling downwards
		if (lastScroll < window.scrollY) {
			if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
				// We're at bottom
				if (isAll) {
					loadPartial('task');
					loadPartial('user');
					loadPartial('category');
				}
				else {
					loadPartial(currentType);
				}
			}
		}
		lastScroll = window.scrollY;
	}

		// Task checkboxes

	handleTaskCheckbox = function(e) {
		Rp('.task-checkbox input[data-task-id]').prop('disabled', true);
		taskID = this.getAttribute('data-task-id');
		mark = Rp.ajaxRequest('api/mark_task');
		mark.onreadystatechange = function() {
			if (mark.readyState == 4) {
				Rp('#taskList').removeClass('loading');
				Rp('.task-checkbox input[data-task-id]').prop('disabled', false);
				response = Rp.parseJSON(mark.responseText);
				if (response.success) {
					e.target.parentNode.parentNode.parentNode.parentNode.parentNode.style.display = 'none';
				}
				else {
					console.log('Failure to update status of task.');
				}
			}
			else {
				Rp('#taskList').addClass('loading');
			}
		}
		mark.post({
			'taskID': taskID,
			'completed': this.checked
		});
	}

	Rp('.task-checkbox input[data-task-id]').on('change', handleTaskCheckbox);


	delreq = Rp.ajax('api/delete_task')
	.complete(function() {
		r = this.responseJSON();
		Rp('article[data-task-id=' + r.task_id + ']').removeClass('loading').hide();
		loadCategory(currentCat);
	});

	deleteTask = function(e) {
		e.preventDefault();
		id = this.getAttribute('data-task-id');
		Rp('#task' + id).addClass('loading');
		delreq.post('task_id=' + parseInt(id));
	}

	Rp('p.delete a').on('click', deleteTask);
});

/*----- Bagian Search ----*/
/*
var search_text = document.getElementById("search_text");

search_text.onmouseover = function()
{
	this.focus();	
}
search_text.onfocus = function()
{
	var search = document.getElementById("search");
	search.className = "active";
}
search_text.onblur = function()
{
	if (search_text.value==="")
	{
		var search = document.getElementById("search");
		search.className = "";
	}
}
*/