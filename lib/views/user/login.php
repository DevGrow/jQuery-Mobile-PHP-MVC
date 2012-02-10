
	<h2>Login</h2>

	<p>Please login to view your order status or support requests.</p>
	
	<form method="post" action="/user/login">
		<div data-role="fieldcontain">
			<label for="email" class="ui-input-text">E-mail Address:</label>
			<input type="text" name="email" class="text" id="email" value="" />
		</div>
		<div data-role="fieldcontain">
			<label for="password">Password:</label>
			<input type="password" name="password" class="text" id="password" />
		</div>
		<div data-role="fieldcontain">
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return_to" value="" />
			<input type="submit" data-icon="check" class="button submit" value="Login">
		</div>
	</form>