<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}
	
	$cat = Category::model()->find("id_kategori=".addslashes($_GET['cat']));

	if ((!$cat)||(!$cat->getEditable($this->currentUserId)))
	{
		// redirect to error page
	} 
	
	$this->header();
	$id = $_GET['cat'];

?>
		<div class="content">
			<div class="add-task">
				<header>
					<h1>Add Task</h1>
				</header>
				<form id="new_tugas" action="new_task" method="post" enctype="multipart/form-data">
					<input name="id_kategori" type="hidden" value="<?php echo $id; ?>">
					<div class="field">
						<label>Task Name</label>
						<input size="25" maxlength="25" name="nama_task" pattern="^[a-zA-Z0-9 ]{1,25}$" id="nama" type="text">
					</div>
					<div class="field">
						<label>Attachment</label>
						<input size="25" name="attachment[]" id="attachment" type="file"  multiple="">
					</div>
					<div class="field">
						<label>Deadline</label>
						<input size="25" name="deadline" id="deadline"  type="text" pattern="^[1-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]$" onclick="datePicker.showCalendar(event);" title="Tahun harus minimal dari tahun 1955." required/>
					</div>
					<div class="field">
						<label>Assignee</label>
						<input size="25" name="assignee" id="assignee" type="text" pattern="^[^;]{5,}(;[^;]{5,})*$">
					</div>
					<div class="field">
						<label>Tag</label>
						<input size="25" name="tag" id="tag" type="text">
					</div>
					<div class="buttons">
						<button type="submit">Save</button>
					</div>
				</form>
			</div>
		</div>
		<?php
			$this->calendar();
		?>
		<script type="text/javascript">
			var id_user = <?php echo $this->currentUserId; ?>;
		</script>
<?php 
	$this->requireJS('datepicker');
	$this->requireJS('tugas');
	$this->footer();
?>
