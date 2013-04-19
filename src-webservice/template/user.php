<article class="user-listing" data-user-id="<?php echo $user->id_user ?>">
	<div class="photo"><img src="<?php echo "upload/user_profile_pict/".$user->avatar; ?>" alt="Profile Photo"></div>
	<div class="name">
		<span class="fullname"><?php echo $user->fullname ?></span><br>
		<a class="username" href="profile.php?id=<?php echo $user->id_user ?>"><?php echo $user->username ?></a>
	</div>
</article>