
		<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">
			<li data-role="list-divider">User Navigation:</li>
			<?php if($user->is_logged){ ?>
			<li><a href="/user">Profile</a></li>
			<li><a href="/user/logout">Logout</a></li>
			<?php }else{ ?>
			<li><a href="/user/register">Register</a></li>
			<li><a href="/user/login">Login</a></li>
			<?php } ?>
			<li data-role="list-divider">Pages:</li>
			<li><a href="/site/about">About</a></li>
			<li><a href="/site/terms">Terms of Service</a></li>
		</ul>