var currentCat;

Rp(function() {

	if (currentCat === undefined || !currentCat) {
		Rp('#addTaskLi').hide();
	}

	if (canDelete === undefined || !canDelete) {
		Rp('#deleteCategoryLi').hide();
	}

	createTaskElement = function(task) {
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
		checkbox = Rp.factory('input').addClass('task-checkbox').prop('type', 'checkbox').prop('checked', task.done);
		space = document.createTextNode(' ');
		mainLink = Rp.factory('a').prop('href', 'tugas.php?id=' + task.id).prop('innerHTML', task.name);

		detailsDiv = Rp.factory('div').addClass('details');

		deadlineP = Rp.factory('p').addClass('deadline');
		deadlineContentSpan = Rp.factory('span').addClass('detail-content').prop('innerHTML', task.date);

		tagsP = Rp.factory('p').addClass('tags');
		task.tags.forEach(function(tag) {
			tagSpan = Rp.factory('span').addClass('tag').prop('innerHTML', tag);
			tagsP.append(tagSpan);
		})

		checkboxSpan.append(checkbox);
		label.append(checkboxSpan).append(space).append(mainLink);
		h1.append(label);
		header.append(h1);

		deadlineP.append(deadlineContentSpan);
		detailsDiv.append(deadlineP).append(tagsP);

		article.append(header).append(detailsDiv);	
		return article;
	}

	createCategoryElement = function(cat) {
		li = Rp.factory('li').prop('id', 'categoryLi' + cat.id);
		a = Rp
			.factory('a')
			.attr('href', 'dashboard.php?cat=' + cat.id)
			.attr('data-category-id', cat.id)
			.attr('data-deletable', cat.canDelete ? 'true' : 'false')
			.prop('innerHTML', cat.name);

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
			response = Rp.parseJSON(response);
			Rp('#dashboardPrimary').removeClass('loading');
			if (response.success) {
				fillTasks(response.tasks);

				Rp('#categoryList li.active').removeClass('active');
				if (response.categoryID) {
					Rp('#categoryTasks').show();
					li = Rp('#categoryLi' + response.categoryID);
					li.addClass('active');
					Rp('#pageTitle').text(response.categoryName);

					if (response.canDeleteCategory)
						Rp('#deleteCategoryLi').show()
				}
				else {
					Rp('#deleteCategoryLi').hide();
					Rp('#addTaskLi').hide();
					Rp('#pageTitle').text('All Tasks');
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

	loadCategory = function(catid) {
		currentCat = catid;
		if (catid) {
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
			history.pushState(state, catname, 'dashboard.php?cat=' + catid);
		}
		else {
			history.pushState(state, 'Dashboard', 'dashboard.php');
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
				response = Rp.parseJSON(del.responseText);
				if (response.success) {
					Rp('#categoryLi' + catid).hide();
					Rp('#categoryList').removeClass('loading');
					goToCategory(0, 'Dashboard');
				}
				else {
					// error
					console.log('Failed to delete category ' + catid);
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
});