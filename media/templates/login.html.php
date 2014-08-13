<?php include(NX_PATH.'media/templates/head.html.php'); ?>

<div class="login">
	<div class="login-header"><img src="media/img/nemex_icon.svg" alt="nemex logo" width="100px" height="100px" /></div>
	<form action="index.php" method="post" class="loginform">
		<input type="text" name="username" placeholder="Username:"/><br/>
		<input type="password" name="password" placeholder="Password:" /><br/>
		<input type="submit" value="Login" />
	</form>
	<footer>
		<p>made by <a href="http://neonelephant.de">neonelephant</a></p>
	</footer>
</div>

<?php include(NX_PATH.'media/templates/foot.html.php'); ?>

