
	<h2>Your Profile</h2>
	
	<div class="profile">
		<p class='field'>
			<label>Name:</label>
			<span><?php echo $profile['name']; ?></span>
		</p>
		<p class='field'>
			<label>E-mail:</label>
			<span><?php echo $profile['email']; ?></span>
		</p>
		<p class='field'>
			<label>Password:</label>
			<span>************</span>
		</p>
	</div>
	<a href="user/edit" data-icon="gear" data-role="button">Update Information</a>