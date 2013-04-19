<!DOCTYPE html>

<html><?php // TODO implement offline HTML5 ?>
	<head>
		<meta charset="utf-8">
		<title><?php echo $this->title ?></title>
		<base href="<?php echo $this->basePath ?>">
		<link rel="stylesheet" href="css/style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
	</head>

	<body>
		<div class="site-container">
			<header class="site-header">
				<h1><a href="dashboard"><?php echo $this->appName ?></a></h1>
				<p><?php echo $this->appTagline ?></p>

				<?php if ($this->loggedIn): ?>
				<nav>
					<ul class="main-links">
						<?php
						$pages = array(
							'dashboard' => 'Dashboard',
							'profile' => $this->currentUser->fullname,
							'logout' => 'Logout'
						);

						foreach ($pages as $page => $label):
							$active = ($this->currentPage == $page);
						?>

						<li class="<?php echo $page ?>-link<?php if ($active) echo ' active' ?>" id="<?php echo $page ?>Li"><?php
							if ($page == 'profile') {
								?><img src="<?php echo "upload/user_profile_pict/".$this->currentUser->avatar; ?>" alt=""><?php
							} ?><a href="<?php echo $page ?>" id="<?php echo $page ?>Link"><?php echo $label ?></a></li>

					<?php endforeach; ?>
					</ul>

					<?php
					if ($this->currentPage == 'search') {
						$q = $_GET['q'];
						$type = $_GET['type'];
					}
					?>
					<div class="search-box">
						<form action="search" method="get" id="searchForm">
							<select name="type" id="searchType">
								<?php
								foreach (array(
									'all' => 'All',
									'task' => 'Tasks',
									'user' => 'Users',
									'category' => 'Categories'
									) as $k => $v) {
									$selected = ($type == $k) ? ' selected' : '';
									echo "<option value=\"$k\"$selected>$v</option>\n";
								}
								?>
							</select>
							<input type="search" name="q" placeholder="Search" value="<?php echo $q ?>" id="searchQuery">
							<button type="submit">Search</button>
						</form>
					</div>
				</nav>
				<?php endif; ?>
			</header>
			<div id="content">
