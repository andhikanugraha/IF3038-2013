<?php $deadline_datetime = new DateTime($task->deadline); ?>

						<article class="task" data-task-id="<?php echo $task->id_task ?>" id="task<?php echo $task->id_task ?>">
							<header>
								<h1>
									<label>
										<span class="task-checkbox"><input type="checkbox" class="task-checkbox" data-task-id="<?php echo $task->id_task ?>"<?php if ($task->status) echo ' checked' ?>></span>
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
										echo '<span class="tag">' . $tag->tag_name . '</span> ';
									} ?>
								</p>
								<?php if ($task->getDeletable($this->currentUserId)): ?>
								<p class="delete">
									<a href="delete_task.php?task_id=<?php echo $task->id_task ?>" data-task-id="<?php echo $task->id_task ?>">delete</a>
								</p>
								<?php endif; ?>
							</div>
						</article>