<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$id = $_SESSION['user_id'];
	$user = User::model()->find("id_user = ".$id, array("username","email","fullname","avatar","birthdate"));

	$birth_date = new Datetime($user->birthdate);

	$this->requireJS('edit_profile');
	$this->requireJS('checker_edit_pass');
	$this->header('Profile', 'profile');
?>	
	<div class="content">
		<div class="profile">
			<header>
				<h1><?php echo $user->fullname; ?></h1>
			</header>

			<section class="profile-details">
				<figure class="profile-image">
					<img src="<?php echo "upload/user_profile_pict/".$user->avatar; ?>" alt="Profile Photo">
				</figure>
				
				<form enctype="multipart/form-data" id="edit_form" action="change_profile_data" method="post">
					<div class="field">
						<span class="label">Username</span>
						<span id="edit_username"><?php echo $user->username; ?></span>
					</div>
					<div class="field">
						<label for="edit_name">Full Name</label>
						<input id="edit_name" name="fullname" pattern="^.+ .+$" type="text" title="Nama lengkap harus terdiri dari 2 kata dipisah oleh sebuah spasi." value="<?php echo $user->fullname; ?>" required>
					</div>
					<div class="field">
						<label for="birth_date">Date of Birth</label>
						<input id="birth_date" name="birthdate" type="text" pattern="^[1-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]$" onclick="datePicker.showCalendar(event);" title="Tahun harus minimal dari tahun 1955." value="<?php echo $birth_date->format('Y-m-j'); ?>" required/>
					</div>
					<div class="field">
						<label for="edit_email">Email</label>
						<input id="edit_email" name="email" pattern="^.+@.+\...+$" type="text" title="Email yang dimasukkan harus benar dan tidak sama dengan sandi." value="<?php echo $user->email; ?>" required>
					</div>
					<div class="field">
						<label for="edit_avatar">Avatar</label>
						<input id="edit_avatar" name="avatar" type="file" title="Avatar yang diupload harus berekstensi jpg atau jpeg.">
					</div>
					<br />
					<div class="field buttons">
						<button type="submit" id="edit_submit">Save Changes</button>
					</div>
				</form>

				<form id="password_form" action="change_user_password" method="post">
					<div class="field">
						<span class="label">Username</span>
						<span id="edit_username"><?php echo $user->username; ?></span>
					</div>
					<div class="field">
						<label for="edit_current_password">Current Password</label>
						<input id="edit_current_password" name="current_password" pattern="^.{8,}$" type="password" title="Sandi minimal harus terdiri dari 8 karakter dan tidak sama dengan username dan email.">							
					</div>
					<div class="field">
						<label for="edit_new_password">New Password</label>
						<input id="edit_new_password" name="new_password" pattern="^.{8,}$" type="password" title="Sandi minimal harus terdiri dari 8 karakter dan tidak sama dengan username dan email.">	
					</div>
					<div class="field">
						<label for="edit_confirm_password">Confirm Password</label>
						<input id="edit_confirm_password" name="confirm_password" pattern="^.{8,}$" type="password" title="Konfirmasi sandi harus sama dengan sandi." required>
					</div>
					<br />
					<div class="field buttons">
						<button type="submit" id="edit_pass_submit">Save Changes</button>
					</div>
				</form>
			</section>
		</div>
	</div>
	<?php
		$this->loadTemplate("calendar");
	?>
<?php
$this->requireJS('datepicker');
$this->requireJS('checker_profile');
$this->footer(array());
