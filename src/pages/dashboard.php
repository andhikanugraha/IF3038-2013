<?php

// Business Logic Here

$cat = (int) $_GET['cat'];

$todoQ = 'status=0';
$doneQ = 'status=1';
$narrowQ = '';

if ($cat) {
	$narrowQ = ' AND id_kategori=' . $cat;
	$currentCat = Category::model()->find('id_kategori=' . $cat);
}

$tasks = Task::model()->findAll();
$todo = Task::model()->findAll($todoQ . $narrowQ);
$done = Task::model()->findAll($doneQ . $narrowQ);

$categories = Category::model()->findAll();

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
					<ul>
						<li class="add-task-link" id="addTaskLink"><a href="newwork.php">New Task</a></li>
					</ul>
				</header>
				<div class="primary" id="dashboardPrimary">
					<section class="tasks current">
						<header>
							<h3><?php echo $pageTitle ?></h3>
						</header>

						<div id="tasksList">
<?php
foreach ($todo as $task):
	$deadline_datetime = new DateTime($task->deadline); ?>

						<article class="task" data-task-id="<?php echo $task->id_task ?>" data-category-="a">
							<header>
								<h1>
									<label>
										<span class="task-checkbox"><input type="checkbox" class="task-checkbox"></span>
										<a href="tugas.php?id=<?php echo $task->id_task ?>"><?php echo $task->nama_task; ?></a>
									</label>
								</h1>
							</header>
							<div class="details">
								<p class="deadline">
									<span class="detail-label">Deadline:</span>
									<span class="detail-content">
										<?php echo $deadline_datetime->format('j F Y') ?>
									</span>
								</p>
								<p class="tags">
									<span class="detail-label">Tag:</span>
									<?php foreach ($task->getTags() as $tag) {
										echo '<span class="tag">' . $tag->tag . '</span>';
									} ?>
								</p>
							</div>
						</article>

<?php endforeach; ?>
						</div>
					</section>

					<section class="tasks completed">
						<header>
							<h3>Completed Tasks</h3>
						</header>

						<div id="completedTasksList">
<?php
foreach ($done as $task):
	$deadline_datetime = new DateTime($task->deadline); ?>

						<article class="task" data-task-id="<?php echo $task->id_task ?>" data-category-="a">
							<header>
								<h1>
									<label>
										<span class="task-checkbox"><input type="checkbox" class="task-checkbox"></span>
										<a href="tugas.php?id=<?php echo $task->id_task ?>"><?php echo $task->nama_task; ?></a>
									</label>
								</h1>
							</header>
							<div class="details">
								<p class="deadline">
									<span class="detail-label">Deadline:</span>
									<span class="detail-content">
										<?php echo $deadline_datetime->format('j F Y'); ?>
									</span>
								</p>
								<p class="tags">
									<span class="detail-label">Tag:</span>
									<?php foreach ($task->getTags() as $tag) {
										echo '<span class="tag">' . $tag->tag . '</span>';
									} ?>
								</p>
							</div>
						</article>

<?php endforeach; ?>
						</div>
					</section>
				</div>
			
				<div class="secondary">
					<section class="categories">
						<header>
							<h3>Categories</h3>
						</header>
						<ul id="categoryList">
							<?php foreach ($categories as $cat): ?>
							<li id="categoryLi<?php echo $cat->id_kategori ?>"<?php if ($currentCat->id_kategori == $cat->id_kategori) echo ' class="active"' ?>><a href="dashboard.php?cat=<?php echo $cat->id_kategori ?>" data-category-id="<?php echo $cat->id_kategori ?>"><?php echo $cat->nama_kategori ?></a></li>
							<?php endforeach; ?>
						</ul>
						<button type="button" id="addCategoryButton">Tambah Kategori</button>
					</section>
				</div>

			</div>

		</div>
		<?php if ($currentCat): ?><script>var currentCat = <?php echo $currentCat->id_kategori ?></script><?php endif; ?>
		<div class="modal-overlay" id="modalOverlay">
			<div class="modal-dialog">
				<a class="close">&times;</a>
				<header><h3>Tambah Kategori</h3></header>
				<form id="newCategoryForm">
					<div class="field">
						<label for="new_category_name">Nama Kategori</label>
						<input id="new_category_name" name="new_category_name" type="text" title="Nama kategori harus diisi." required />
					</div>
					<div class="field">
						<label for="new_category_username">Username</label>
						<input id="new_category_username" name="new_category_username" pattern="^[^;]{5,}(;[^;]{5,})*$" type="text" title="Username harus terdaftar dan dipisahkan tanda titik-koma. Kosongkan jika private." />
					</div>
					<div class="buttons">
						<button type="submit" id="submitButton" title="Semua elemen form harus diisi dengan benar dahulu.">Simpan</button>
					</div>
				</form>
			</div>
		</div>
<?php $this->footer() ?>