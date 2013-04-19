<?php
	if ($this->loggedIn) 
	{
		header('Location: dashboard');
		return;
	}

	$this->header();
?>
		<div class="content">
			<div class="index">	
				<header>
					<ul>
						<li class="login"><a href="#" id="loginLink">Login</a></li>
					</ul>
				</header>
				
				<section class="login-box" id="loginBox">
					<header>
						<h3>Login</h3>
					</header>
					<form id="login_form" action="#" method="post" class="vertical">
						<div class="field">
							<label>Username</label>
							<input size="30" maxlength="50" name="username" id="login_username" type="text">
						</div>
						<div class="field">
							<label>Password</label>
							<input size="30" maxlength="50" name="password" id="login_password" type="password">
						</div>
						<div class="field">
							<button type="submit" id="loginButton">Login</button>
						</div>
					</form>
				</section>
				
				<div class="primary">
					<section class="welcome">
						<div class="intro">
							<div id="intro-slideshow">
								<div id="slide_left">
								</div>
								<div id="content_wrap_inner">
									<img class="slide_item" src="<?php echo $_SESSION['base_url']; ?>images/tes.jpg" alt="gambar 1">
									<img class="slide_item" src="<?php echo $_SESSION['base_url']; ?>images/tes2.jpg" alt="gambar 2">
									<img class="slide_item" src="<?php echo $_SESSION['base_url']; ?>images/tes3.jpg" alt="gambar 3">
									<img class="slide_item" src="<?php echo $_SESSION['base_url']; ?>images/tes4.jpg" alt="gambar 4">
								</div>
								<div id="slide_right">
								</div>
								<div class="clear"></div>
							</div>
							<?php
								//<h2>Easy Task Management</h2>
								//<p>DO lets you focus on the things you're supposed to do.</p>
							?>
						</div>
					</section>
				</div>
			
				<div class="secondary">
					<section class="register">
						<header>
							<h3>Sign Up Now!</h3>
						</header>

						<form enctype="multipart/form-data" id="register_form" action="register" method="post" class="vertical">
							<div class="field">
								<label for="register_username">Username</label>
								<input id="register_username" name="username" pattern="^.{5,}$" type="text" title="Username minimal harus terdiri dari 5 karakter dan tidak sama dengan sandi." required data-neq="register_password">
							</div>
							<div class="field">
								<label for="register_password">Password</label>
								<input id="register_password" name="password" pattern="^.{8,}$" type="password" title="Sandi minimal harus terdiri dari 8 karakter dan tidak sama dengan username dan email." required data-neq="register_username">							
							</div>
							<div class="field">
								<label for="register_confirm_password">Confirm Password</label>
								<input id="register_confirm_password" name="confirm_password" pattern="^.{8,}$" type="password" title="Konfirmasi sandi harus sama dengan sandi." required>
							</div>
							<div class="field">
								<label for="register_name">Full Name</label>
								<input id="register_name" name="fullname" pattern="^.+ .+$" type="text" title="Nama lengkap harus terdiri dari 2 kata dipisah oleh sebuah spasi." required>
							</div>
							<div class="field">
								<label for="birth_date">Date of Birth</label>
								<input id="birth_date" name="birthdate" type="text" pattern="^[1-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]$" title="Tahun harus minimal dari tahun 1955." required/>
							</div>
							<div class="field">
								<label for="register_email">Email</label>
								<input id="register_email" name="email" pattern="^.+@.+\...+$" type="text" title="Email yang dimasukkan harus benar dan tidak sama dengan sandi." required>
							</div>
							<div class="field">
								<label for="register_avatar">Avatar</label>
								<input id="register_avatar" name="avatar" type="file" title="Avatar yang diupload harus berekstensi jpg atau jpeg." required>
							</div>
							<div class="field buttons">
								<button type="submit" id="register_submit">Register</button>
							</div>
						</form>
					</section>
				</div>

			</div>

		</div>
		<?php
			$this->calendar();
		?>
<?php
$this->requireJS('index');
$this->requireJS('datepicker');
$this->requireJS('request');
$this->requireJS('login');
$this->requireJS('register');
//$this->requireJS('checker');
$this->footer(array());
