<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}
	
	if (ISSET($_GET['id']))
	{
		$task = Task::model()->find("id_task = ".addslashes($_GET['id']));
		if (!$task)
		{
			// redirect to error page
		}
		$users = $task->getAssignee();
	}
	else
	{
		// redirect to error page
	}
	
	$this->header('Dashboard', 'dashboard');
?>

		<div class="content">
			<div class="task-details not-editing">
				<header>
					<form method="POST">
						<h1>
							<label>
								<span class="task-checkbox"><input type="checkbox" class="task-checkbox"></span>
								<span class="task-title"><?php echo $task->nama_task ?></span>
							</label>
						</h1>
					</form>

					<ul>
						<li><a href="#" id="editTaskLink">Edit Task</a></li>
					</ul>
				</header>
				<div id="current-task">
					<section class="details">
						<header>
							<h3>Details</h3>
						</header>
						<?php
						/*
						<p class="description">
							<span class="detail-label">Description:</span>
							<span>Lorem ipsum dolor sit amet, task description goes here.</span>
						</p>
						*/ ?>
						<p class="assignee">
							<span class="detail-label">Assignee:</span>
							<span class="detail-content">
								<?php 
									$string = "";
									foreach ($users as $user)
									{
										$string .= "<a href='profile?id=".$user->id_user."'>".$user->username."</a>,"; 
									}
									$string = substr($string, 0, -1);
									echo $string;
								?>
							</span>
						</p>
						<p class="category">
							<span class="detail-label">Kategori:</span>
							<span class="detail-content"><?php echo $task->getCategory()->nama_kategori; ?></span>
						</p>
						<p class="tags">
							<span class="detail-label">Tag:</span>
							<span class="tag">satu</span>
							<span class="tag">dua</span>
							<span class="tag">tiga</span>
							<span class="tag">empat</span>
						</p>
					</section>
					<section class="attachment">
						<header>
							<h3>Attachment</h3>
						</header>
						<figure>
							<img src="assets/photo.jpg" alt="">
						</figure>
					</section>
				</div>
				<div id="edit-task">
					<form id="new_tugas" action="#" method="post">
						<div class="field">
							<label>Task Name</label>
							<input size="25" maxlength="25" name="nama" id="nama" type="text">
						</div>
						<div class="field">
							<label>Attachment</label>
							<input name="attachment" id="attachment" type="file">
						</div>
						<div class="field">
							<label>Deadline</label>
							<input name="deadline" id="deadline" type="date">
						</div>
						<div class="field">
							<label>Assignee</label>
							<input name="assignee" id="assignee" type="text">
						</div>
						<div class="field">
							<label>Tag</label>
							<input name="tag" id="tag" type="text">
						</div>
						<div class="buttons">
							<button type="submit">Save</button>
						</div>
					</form>
				</div>
				<section class="comments">
					<header>
						<h3>2 Comments</h3>
					</header>

					<div id="commentsList">
						<article class="comment">
							<header>
								<h4>Komentator</h4>
							</header>
							<p>Lorem ipsum dolor sit amet.</p>
						</article>

						<article class="comment">
							<header>
								<h4>Komentator</h4>
							</header>
							<p>Lorem ipsum dolor sit amet.</p>
						</article>
					</div>

					<div class="comment-form">
						<h3>Add Comment</h3>
						<form id="commentForm" action="#" method="post">
							<textarea name="komentar" id="commentBody"></textarea>
							<button type="submit">Send</button>
						</form>
					</div>
				</section>
			</div>
		</div>
<?php $this->footer() ?>
