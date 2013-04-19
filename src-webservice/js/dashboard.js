var currentCat;
var canDelete;
var taskCache;

Rp(function() 
{

	if (currentCat === undefined || !currentCat) {
		Rp('#addTaskLi').hide();
		currentCat = 0;
	}

	if (canDelete === undefined || !canDelete) {
		Rp('#deleteCategoryLi').hide();
		canDelete = false;
	}

	createTaskElement = function(task) 
	{
		// Parse and validate param
		if (task.tags === undefined)
			task.tags = [];
		if (task.done === undefined)
			task.done = false;
		if (task.date === undefined)
			task.date = '';

		// Main logic
		article = Rp.factory('article').addClass('task').attr('data-task-id', task.id);

		header = Rp.factory('header');
		h1 = Rp.factory('h1');
		label = Rp.factory('label');
		checkboxSpan = Rp.factory('span').addClass('task-checkbox');
		checkbox = Rp.factory('input')
			.addClass('task-checkbox')
			.prop('type', 'checkbox')
			.prop('checked', task.done)
			.attr('data-task-id', task.id)
			.on('click', handleTaskCheckbox);
		space = document.createTextNode(' ');
		mainLink = Rp.factory('a').prop('href', 'tugas?id=' + task.id).text(task.name);

		detailsDiv = Rp.factory('div').addClass('details');

		deadlineP = Rp.factory('p').addClass('deadline');
		deadlineContentSpan = Rp.factory('span').addClass('detail-content').text(task.deadline);

		tagsP = Rp.factory('p').addClass('tags');
		task.tags.forEach(function(tag) {
			tagSpan = Rp.factory('span').addClass('tag').text(tag);
			tagsP.append(tagSpan);
			tagsP.html(tagsP.html() + ' ');
		})

		checkboxSpan.append(checkbox);
		label.append(checkboxSpan).append(space).append(mainLink);
		h1.append(label);
		header.append(h1);

		deadlineP.append(deadlineContentSpan);
		detailsDiv.append(deadlineP).append(tagsP);

		if (task.deletable) {
			delP = Rp.factory('p').addClass('delete');
			delA = Rp.factory('a')
			.attr('data-task-id', task.id)
			.prop('href', 'delete.php?task_id=' + task.id)
			.text('delete');
			delA.on('click', deleteTask);
			delP.append(delA);
			detailsDiv.append(delP);
		}

		article.append(header).append(detailsDiv);	
		return article;
	}

	createCategoryElement = function(cat) {
		if (cat.id == undefined)
			cat.id = 0;
		li = Rp.factory('li').prop('id', 'categoryLi' + cat.id);
		a = Rp
			.factory('a')
			.attr('href', 'dashboard.php?cat=' + cat.id)
			.attr('data-category-id', cat.id)
			.attr('data-deletable', cat.canDeleteCategory ? 'true' : 'false')
			.text(cat.name);

		li.append(a);

		return li;
	}

	fillTasks = function(tasks) {
		tasksList = Rp('#tasksList');
		tasksList.empty();
		completedTasksList = Rp('#completedTasksList');
		completedTasksList.empty();
		tasks.forEach(function(task) {
			if (task.done)
				completedTasksList.append(createTaskElement(task));
			else
				tasksList.append(createTaskElement(task));
		});
	}

	fillCategories = function(cats) {
		catsList = Rp('#categoryList');
		catsList.empty();
		cats.forEach(function(cat) {
			catsList.append(createCategoryElement(cat));
		});
	}

	catreq = Rp.ajaxRequest();
	catreq.onreadystatechange = function() {
		if (catreq.readyState == 4) {
			// Loaded
			response = catreq.responseText;
			taskCache = response;
			response = Rp.parseJSON(response);
			Rp('#dashboardPrimary').removeClass('loading');
			if (response.success) {
				fillTasks(response.tasks);

				Rp('#categoryList li.active').removeClass('active');
				if (response.categoryID) 
				{
					Rp('#categoryTasks').show();
					li = Rp('#categoryLi' + response.categoryID);
					li.addClass('active');
					Rp('#pageTitle').text(response.categoryName);

					if (response.canDeleteCategory)
					{
						Rp('#addTaskLi').css('display', 'block');
						Rp('#addTaskCat').prop('href', 'new_work?cat='+response.categoryID);
						Rp('#deleteCategoryLi').css('display', 'block');
					}
					else if (response.canEditCategory)
					{
						Rp('#addTaskLi').css('display', 'block');
						Rp('#addTaskCat').prop('href', 'new_work?cat='+response.categoryID);
						Rp('#deleteCategoryLi').css('display', 'none');
					}
					else
					{
						Rp('#addTaskLi').css('display', 'none');
						Rp('#deleteCategoryLi').css('display', 'none');
					}
				}
				else 
				{
					Rp('#addTaskLi').css('display', 'none');
					Rp('#deleteCategoryLi').css('display', 'none');
					Rp('#pageTitle').text('All Tasks');
					li = Rp('#categoryLi0');
					li.addClass('active');
				}
			}
			else {
				loadCategory(0);
			}
		}
		else if (catreq.readyState > 0) {
			// Still loading
			Rp('#dashboardPrimary').addClass('loading');
		}
	}

	loadCategory = function(catid) 
	{
		currentCat = catid;
		if (catid!=0) {
			catreq.get('api/retrieve_tasks?category_id=' + catid);
		}
		else {
			catreq.get('api/retrieve_tasks');
		}
	}

	goToCategory = function(catid, catname) {
		state = {
			'categoryID' : catid,
			'categoryName' : catname
		};
		if (catid != 0) {
			history.pushState(state, catname, 'dashboard?cat=' + catid);
		}
		else {
			history.pushState(state, 'Dashboard', 'dashboard');
		}
		loadCategory(catid);
	}

	Rp('#categoryList li a').on('click', function(e) {
		e.preventDefault();
		catid = this.getAttribute('data-category-id');
		goToCategory(catid, this.innerHTML);
	});

	window.onpopstate = function(e) {
		console.log(e);
		if (!e.state)
			loadCategory(0);
		else {
			catid = e.state.categoryID;
			if (catid !== undefined)
				loadCategory(catid);
		}
	}

	showModal = function() {
		Rp('#modalOverlay').css('display', 'block');
		window.setTimeout(function() {
			Rp('#modalOverlay').addClass('visible');
		}, 100);
	}
	hideModal = function() {
		Rp('#modalOverlay').removeClass('visible').css('display', 'none');
	}
	Rp('.modal-overlay .close').on('click', function() {
		Rp(this.parentNode.parentNode).removeClass('visible').css('display', 'none');
	})

	// Adding categories
	Rp('#addCategoryButton').on('click', function() {
		showModal();
	});
	Rp('#newCategoryForm').on('submit', function(e) {
		e.preventDefault();

		serialized = Rp(this).serialize();

		req = Rp.ajaxRequest('api/add_category');
		req.onreadystatechange = function() {
			switch (req.readyState) {
				case 1:
				case 2:
				case 3:
					Rp('#newCategoryForm').addClass('loading');
					break;
				case 4:
					Rp('#newCategoryForm').removeClass('loading');
					try {
						response = Rp.parseJSON(req.responseText);
						fillCategories(response.categories);
						goToCategory(response.categoryID, response.categoryName);
					}
					catch (e) {

					}
					hideModal();
					break;
			}
		}
		req.post(serialized);
	});

	// Delete category
	deleteCategory = function(catid) {
		qs = 'category_id=' + catid;
		del = Rp.ajaxRequest('api/delete_category');
		del.onreadystatechange = function() {
			if (del.readyState == 4) {
				Rp('#categoryList').removeClass('loading');
				response = Rp.parseJSON(del.responseText);
				if (response.success) {
					Rp('#categoryLi' + catid).hide();
					goToCategory(0, 'Dashboard');
				}
				else {
					// error
					console.log('Failed to delete category ' + catid);
					Rp('#categoryList').removeClass('loading');
				}
			}
			else {
				Rp('#categoryList').addClass('loading');
			}
		}
		del.post(qs);
	}

	Rp('#deleteCategoryLink').on('click', function(e) {
		e.preventDefault();
		if (confirm('Yakin hapus kategori ini?'))
			deleteCategory(currentCat);
	});

	// Task checkboxes

	handleTaskCheckbox = function() {
		Rp('.task-checkbox input[data-task-id]').prop('disabled', true);
		taskID = this.getAttribute('data-task-id');
		mark = Rp.ajaxRequest('api/mark_task');
		mark.onreadystatechange = function() {
			if (mark.readyState == 4) {
				Rp('#dashboardPrimary').removeClass('loading');
				response = Rp.parseJSON(mark.responseText);
				if (response.success) {
					loadCategory(currentCat);
				}
				else {
					console.log('Failure to update status of task.');
				}
			}
			else {
				Rp('#dashboardPrimary').addClass('loading');
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

	// Regular refresh
	var ref;
	intreq = Rp.ajax()
		.complete(function() {
			// Loaded
			response = catreq.responseText;
			if (response != taskCache) {
				console.log('New tasks found.');
				taskCache = response;
				response = this.responseJSON();
				if (response.success)
					fillTasks(response.tasks);
			}
		});

	refreshTasks = function() {
		console.log('Checking for new tasks...');
		catid = currentCat;
		if (catreq.readyState)
			return;
		if (catid!=0) {
			intreq.get('api/retrieve_tasks?category_id=' + catid);
		}
		else {
			intreq.get('api/retrieve_tasks');
		}
	}

	var ref = window.setInterval(refreshTasks, 5000);

	// Assignee
	var asignee = document.getElementById("usernames_list");
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

	choose_assignee = function(username)
	{
		var elm = document.getElementById("auto_comp_assignee");
		var value = asignee.value;
		value = value.substr(0, value.lastIndexOf(",")+1);
		value += username;
		asignee.value = value;
		elm.style.display = "none";
	}
});
