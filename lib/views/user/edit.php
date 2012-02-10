
	<h2>Update Contact Info</h2>
	<p><strong>Note:</strong> Only enter your password if you wish to change it, otherwise leave it blank.</p>
	<form action='/user/edit' method='post' class='register-form'>
		<p>
			<label for='name'>Name:</label>
			<input type='text' name='name' class='text' id='name' value='<?php __('user_name'); ?>' />
		</p>
		<p>
			<label for='email'>E-mail Address:</label>
			<input type='text' name='email' class='text' id='email' value='<?php __('user_email'); ?>' />
		</p>
		<p>
			<label for='password'>Password:</label>
			<input type='password' name='password' class='text' id='password' />
		</p>
		<p>
			<label for='password2'>Confirm Password:</label>
			<input type='password' name='password2' class='text' id='password2' />
		</p>
		<p>
			<label></label>
			<input type='submit' class='button contact' value='Update' />
		</p>
	</form>