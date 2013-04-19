<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	// Business Logic Here

	$cat = (int) $_GET['cat'];

	$todoQ = 'status=0';
	$doneQ = 'status=1';
	$narrowQ = '';

	if ($cat) {
		$currentCat = Category::model()->findAll('id_kategori=' . $cat);
		//$currentCat = Category::model()->find('id_user=' . $this->currentUserId);
		if ($currentCat) {
			$currentCat = $currentCat[0];
			$narrowQ = ' AND id_kategori=' . $cat;
			$canDelete = $currentCat->id_user == $this->currentUserId;
		}
		else {
			unset($currentCat);
			unset($cat);
		}
	}

	//$tasks = $this->currentUser->getTasks();
	$todo = $this->currentUser->getTasks($todoQ . $narrowQ);
	$done = $this->currentUser->getTasks($doneQ . $narrowQ);

	$categories = $this->currentUser->getCategories();

	// Presentation Logic Here

	if ($cat) {
		$pageTitle = $currentCat->nama_kategori;
	}
	else {
		$pageTitle = 'All Tasks';
	}

	$this->requireJS('checker');
	$this->requireJS('dashboard');
	$this->header('Dashboard', 'dashboard');
?>
		<div class="content">
			<div class="dashboard">	
				<header>
					<h1>Dashboard</h1>
					<ul id="categoryTasks">
						<li class="add-task-link" id="deleteCategoryLi"><a href="newwork.php" id="deleteCategoryLink">Delete Category</a></li>
						<li class="add-task-link" id="addTaskLi"><a id="addTaskCat" href="new_work">New Task</a></li>
					</ul>
				</header>
				<div class="primary" id="dashboardPrimary">
					<form action="" method="POST">
					<section class="tasks current">
						<header>
							<h3 id="pageTitle"><?php echo $pageTitle ?></h3>
						</header>

						<div id="tasksList">
<?php foreach ($todo as $task) { include dirname(__FILE__) . '/../template/task.php'; } ?>
						</div>
					</section>

					<section class="tasks completed">
						<header>
							<h3>Completed Tasks</h3>
						</header>

						<div id="completedTasksList">
<?php
foreach ($done as $task) { include dirname(__FILE__) . '/../template/task.php'; } ?>
						</div>
					</section>
					</form>
				</div>
			
				<div class="secondary">
					<section class="categories">
						<header>
							<h3>Categories</h3>
						</header>
						<ul id="categoryList">
							<li id="categoryLi0" <?php if ($currentCat->id_kategori == 0) echo ' class="active"';?>><a href="dashboard.php" data-category-id="<?php echo 0; ?>">All Tasks</a></li>
							<?php foreach ($categories as $cat): ?>
							<li data-deletable="<?php echo $cat->id_user == $this->currentUserId ? 'true' : 'false' ?>" id="categoryLi<?php echo $cat->id_kategori ?>"<?php if ($currentCat->id_kategori == $cat->id_kategori) echo ' class="active"' ?>><a href="dashboard.php?cat=<?php echo $cat->id_kategori ?>" data-category-id="<?php echo $cat->id_kategori ?>"><?php echo $cat->nama_kategori ?></a></li>
							<?php endforeach; ?>
						</ul>
						<button type="button" id="addCategoryButton">Tambah Kategori</button>
					</section>
				</div>

			</div>

		</div>
		<?php if ($currentCat): ?><script>
			var currentCat = <?php echo $currentCat->id_kategori ?>;
			var canDelete = <?php echo json_encode($canDelete) ?>;
		</script><?php endif; ?>
		<div class="modal-overlay" id="modalOverlay">
			<div class="modal-dialog">
				<a class="close">&times;</a>
				<header><h3>Tambah Kategori</h3></header>
				<form id="newCategoryForm">
					<div class="field">
						<label for="nama_kategori">Nama Kategori</label>
						<input id="nama_kategori" name="nama_kategori" type="text" title="Nama kategori harus diisi." required />
					</div>
					<div class="field">
						<label for="usernames_list">Username</label>
						<input id="usernames_list" name="usernames_list" pattern="^[^;]{5,}(;[^;]{5,})*$" type="text" title="Username harus terdaftar dan dipisahkan tanda titik-koma. Kosongkan jika private." />
						<div id="auto_comp_assignee">
							<ul id="auto_comp_inflate_assignee"></ul>
						</div>
					</div>
					<div class="buttons">
						<button type="submit" title="Semua elemen form harus diisi dengan benar dahulu.">Simpan</button>
					</div>
				</form>
			</div>
		</div>
<?php $this->footer() ?>
