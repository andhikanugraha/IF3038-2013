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
		$tags = $task->getTags();
		$attachments = $task->getAttachment();
		$comments = $task->getComment();
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
							<label><span class="task-checkbox"><input type="checkbox" class="task-checkbox" data-task-id="<?php echo $task->id_task ?>"></span>
								<span id="task-title" class="task-title"><?php echo $task->nama_task; ?></span>
							</label>
						</h1>
					</form>

					<ul>
						<?php
							if ($task->getDeletable($this->currentUserId))
								echo '<li><a href="#" id="removeTaskLink">Remove Task</a></li>';
						?>
						<?php
							if ($task->getEditable($this->currentUserId))
								echo '<li><a href="#" id="editTaskLink">Edit Task</a></li>';
						?>
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
						<p class="deadline">
							<span class="detail-label">Deadline:</span>
							<span id="detail-deadline" class="detail-content"><?php echo (new DateTime($task->deadline))->format('j F Y'); ?></span>
						</p>
						<p class="assignee">
							<span class="detail-label">Assignee:</span>
							<span id="detail-asignee" class="detail-content">
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
						<p id="detail-tag" class="tags">
							<span class="detail-label">Tag:</span>
							<?php
								foreach ($tags as $tag)
								{
									echo '<span class="tag">';
									echo $tag->tag_name;
									echo '</span>';
								}
							?>
						</p>
					</section>
					<section class="attachment">
						<header>
							<h3>Attachment</h3>
						</header>
						<?php
							$i = 1;
							foreach ($attachments as $attachment)
							{
								?>
								<?php
									$atch = explode(".", $attachment->attachment);
									if (strtoupper($atch[count($atch)-1]) == "MP4") {
									?>
										<video class="atchmt" controls>
										<source src="upload/attachments/<?php echo $attachment->attachment ?>" type="video/mp4">
									<?php
									}
									else if ((strtoupper($atch[count($atch)-1]) == "JPG") or (strtoupper($atch[count($atch)-1]) == "PNG") or (strtoupper($atch[count($atch)-1]) == "GIF")){								
								?>
								<img class="atchmt" src="upload/attachments/<?php echo $attachment->attachment ?>" alt="Task Attachment">
							<?php
									}
									else {
										$file = "<a href=upload/attachments/".$attachment->attachment.">Download attachment-".$i." here</a>,";
										echo $file;
									}
								$i++;
							}
							?>
					</section>
				</div>
				<div id="edit-task">
					<form id="new_tugas" action="edit_tugas" method="post" enctype="multipart/form-data">
						<input type="hidden" name="id_task" value="<?php echo $task->id_task; ?>" >
						<div class="field">
							<label>Task Name</label>
							<input size="25" maxlength="25" name="nama_task" id="nama" pattern="^[a-zA-Z0-9 ]{1,25}$" type="text" value="<?php echo $task->nama_task; ?>">
						</div>
						<div class="field">
							<label>Attachment</label>
							<input size="25" name="attachment[]" id="attachment" type="file" multiple="">
						</div>
						<?php
							$i = 1;
							foreach ($attachments as $attachment)
							{
								echo '<div class="field">';
								echo '<span class="detail-label">Attachment '.$i.':</span>';
								echo '<span class="detail-content">';
								echo $attachment->attachment;
								echo '</span>';
								echo '</div>';
								$i++;
							}
						?>
						<div class="field">
							<label>Deadline</label>
							<input size="25" name="deadline" id="deadline" type="text" value="<?php echo (new DateTime($task->deadline))->format('Y-m-j'); ?>">
						</div>
						<div class="field">
							<label>Assignee</label>
							<input size="25" name="assignee" id="assignee" type="text"  autocomplete="off" pattern="^[^;]{5,}(;[^;]{5,})*$" 
							value="<?php 
									$string = "";
									foreach ($users as $user)
									{
										$string .= $user->username.","; 
									}
									$string = substr($string, 0, -1);
									echo $string;
								?>">
							<div id="auto_comp_assignee">
								<ul id="auto_comp_inflate_assignee"></ul>
							</div>
						</div>
						<div class="field">
							<label>Tag</label>
							<input size="25" name="tag" id="tag" type="text" autocomplete="off" value="<?php 
									$string = "";
									foreach ($tags as $tag)
									{
										$string .= $tag->tag_name.",";
									}
									$string = substr($string, 0, -1);
									echo $string;
								?>">
							<div id="auto_comp_tag">
								<ul id="auto_comp_inflate_tag"></ul>
							</div>
						</div>
						<div class="buttons">
							<button type="submit">Save Changes</button>
						</div>
					</form>
				</div>
				<section class="comments">
					<header>
						<h3 id="total_comment"><?php $total_comment = $task->getTotalComment(); echo $total_comment; ?> Comment<?php echo ($total_comment>1)? "s" : ""; ?></h3>
						<?php
							if ($total_comment>10)
							{
						?>
							<a id="more_link" href="javascript:more_comment()">Previous Comment<?php echo ($total_comment-10>1)? "s" : ""; ?></a>
						<?php
							}
						?>
						<span id="show_status" class="right">Showing <?php echo min(10,$total_comment);?> of <?php echo $total_comment; ?></span>
						<div class="clear"></div>
					</header>
	
					<div id="commentsList">
						<?php
							if ($total_comment>0)
								$firsttimestamp = $comments[0]->timestamp;
							else
								$firsttimestamp = (new DateTime())->format("Y-m-d G:i:s");
							$lasttimestamp = (new DateTime())->format("Y-m-d G:i:s");;
							foreach ($comments as $comment)
							{
								$user = $comment->getUser();
								echo '<article id="comment_'.$comment->id_komentar.'" class="comment">';
									echo '<a href="profile?id='.$user->id_user.'">';
										echo '<img src="'."upload/user_profile_pict/".$user->avatar.'" alt="'.$user->fullname.'" class="icon_pict" >';
									echo '</a>';
									echo '<div class="right">';
										echo (new DateTime($comment->timestamp))->format('H:i – D/M');
										if ($user->id_user==$this->currentUserId)
											echo ' <a href="javascript:delete_comment('.$comment->id_komentar.')">DELETE</a>';
									echo '</div>';
									echo '<header>';
										echo '<a href="profile?id='.$user->id_user.'">';
											echo '<h4>'.$user->username.'</h4>';
										echo '</a>';
									echo '</header>';
									echo '<p>';
										echo $comment->komentar;
									echo '</p>';
									echo '<div class="clear"></div>';
								echo '</article>';
								$lasttimestamp = $comment->timestamp;
							}
						?>
					</div>

					<div class="comment-form">
						<h3>Add Comment</h3>
						<?php
							echo '<a href="profile">';
								echo '<img src="'."upload/user_profile_pict/".$this->currentUser->avatar.'" alt="'.$this->currentUser->fullname.'" class="icon_pict" >';
							echo '</a>';
						?>
						<form id="commentForm" action="#" method="post">
							<input type="hidden" name="id_task" value="<?php echo $task->id_task; ?>">
							<textarea name="komentar" id="commentBody"></textarea>
							<button type="submit">Send</button>
						</form>
					</div>
				</section>
			</div>
		</div>
		<?php
			$this->calendar();
		?>
		<script type="text/javascript">
			var id_task = <?php echo $task->id_task; ?>;
			var first_timestamp = "<?php echo $firsttimestamp; ?>";
			var timestamp = "<?php echo $lasttimestamp; ?>";
			var total_comment = <?php echo $total_comment; ?>;
			var id_user = <?php echo $this->currentUserId; ?>;
			var current_total_comment = <?php echo min(10,$total_comment);?>;
		</script>
<?php
	$this->requireJS('datepicker');
	$this->requireJS('tugas');
	$this->requireJS('comment');
	$this->footer();
?>
