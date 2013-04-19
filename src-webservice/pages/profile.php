<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$id = (ISSET($_GET['id'])) ? $_GET['id'] : $_SESSION['user_id'];
	$user = User::model()->find("id_user = ".$id, array("id_user","username","email","fullname","avatar","birthdate"));
	$tasks = $user->getAssignedTasks();
	
	$birth_date = new Datetime($user->birthdate);

	$this->header('Profile', $_GET['id'] && $_GET['id'] != $_SESSION['user_id'] ? '' : 'profile');
?>	
	<div class="content">
		<div class="profile">
			<header>
				<h1><?php echo $user->fullname; ?></h1>
				<?php
					if ($id == $_SESSION['user_id'])
					{
				?>
						<ul>
							<li class="edit-profile-link"><a href="edit_profile" id="editProfileButton">Edit Profile</a></li>
							<li class="edit-profile-link"><a href="change_password">Change Password</a></li>
						</ul>
				<?php
					}
				?>
			</header>

			<section class="profile-details">
				<figure class="profile-image">
					<img src="<?php echo "upload/user_profile_pict/".$user->avatar; ?>" alt="Profile Photo">
				</figure>
				<?php
				/*
				<p class="description">
					<span class="detail-label">About Me:</span>
					<span class="detail-value">Lorem Ipsum Dolor Sit Amet</span>
				</p>
				*/
				?>
				<p class="username">
					<span class="detail-label">Username:</span>
					<span class="detail-value"><?php echo $user->username; ?></span>
				</p>
				<p class="email">
					<span class="detail-label">Email:</span>
					<span class="detail-value"><?php echo $user->email; ?></span>
				</p>
				<p class="date-of-birth">
					<span class="detail-label">Date of Birth:</span>
					<span class="detail-value"><?php echo $birth_date->format('j F Y'); ?></span>
				</p>
			</section>

			<section class="tasks current">
				<header>
					<h3>Current Tasks</h3>
				</header>

				<?php
					$check = true;
					foreach ($tasks as $task):
						if ($task->status == 0)
						{
							$check = false;
							$deadline_datetime = new DateTime($task->deadline); 
				?>
						<article class="task" data-task-id="<?php echo $task->id_task; ?>" data-category="<?php echo $task->getCategory()->nama_kategori; ?>">
							<header>
								<h1>
									<label>
										<a href="<?php echo "tugas?id=".$task->id_task; ?>"><?php echo $task->nama_task; ?></a>
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
									<?php 
										$tags = $task->getTags();
										foreach ($tags as $tag) 
										{
											echo '<span class="tag">' . $tag->tag_name . '</span>';
										} 
									?>
								</p>
							</div>
						</article>
				<?php
						}
					endforeach;
					
					if ($check)
					{
						echo "No task currently.";
					}
				?>
			</section>

			<section class="tasks completed">
				<header>
					<h3>Completed Tasks</h3>
				</header>

				<?php
					$check = true;
					foreach ($tasks as $task):
						if ($task->status == 1)
						{
							$check = false;
							$deadline_datetime = new DateTime($task->deadline); 
				?>
						<article class="task" data-task-id="<?php echo $task->id_task; ?>" data-category="<?php echo $task->getCategory()->nama_kategori; ?>">
							<header>
								<h1>
									<label>
										<a href="<?php echo "tugas?id=".$task->id_task; ?>"><?php echo $task->nama_task; ?></a>
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
									<?php 
										$tags = $task->getTags();
										foreach ($tags as $tag) 
										{
											echo '<span class="tag">' . $tag->tag_name . '</span>';
										} 
									?>
								</p>
							</div>
						</article>
				<?php
						}
					endforeach; 
					
					if ($check)
					{
						echo "No task completed.";
					}
				?>
			</section>
		</div>
	</div>
<?php
$this->footer(array());
