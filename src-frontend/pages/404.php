<?php
	$this->header();
?>
	<div class="content">
		<div class="error">
			<header>
				<h1>Error</h1>
			</header>
			<p><?php echo $message ? $message : 'Something awkward happened.' ?></p>
		</div>
	</div>
<?php
	$this->footer();
	exit;
?>
