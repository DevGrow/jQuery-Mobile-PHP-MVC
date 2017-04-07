		<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">
			<li data-role="list-divider">User Navigation:</li>
			<?php if($user->is_logged){ ?>
			<li><a href="<?php echo BASE_URL; ?>/user">Profile</a></li>
			<li><a href="<?php echo BASE_URL; ?>/user/logout">Logout</a></li>
			<?php }else{ ?>
			<li><a href="<?php echo BASE_URL; ?>/user/register">Register</a></li>
			<li><a href="<?php echo BASE_URL; ?>/user/login">Login</a></li>
			<?php } ?>
			<li data-role="list-divider">Pages:</li>
			<li><a href="<?php echo BASE_URL; ?>/site/about">About</a></li>
			<li><a href="<?php echo BASE_URL; ?>/site/terms">Terms of Service</a></li>
		</ul>
