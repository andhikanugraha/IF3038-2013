<?php
	if (!$this->loggedIn) 
	{
		header('Location: index');
		return;
	}

	$id = $_SESSION['user_id'];
	$user = User::model()->find("id_user = ".$id, array("username","email","fullname","avatar","birthdate"));

	$birth_date = new Datetime($user->birthdate);

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
				
				<form id="edit_form" action="change_user_password" method="post">
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
$this->requireJS('checker_edit_pass');
$this->footer(array());
