<?php require view('static/header_html'); ?>
	<div id="page" class="site">

<section class="decorative right-dark">
	<div class="light-side">

		<div class="side-content">

			<h2>RESET PASSWORD</h2>
			<p>Please enter a new password that's strong and you don't forget.</p>

			<?php

				foreach ($errors as $error)
					echo $error."<br>";

				if (count($errors) > 0) echo "<br>";
			?>

			<form method="post" action="">
				<div class="wrap">
					<div class="col xl-11-12">

				        <input type="hidden" name="email" value="<?=$email?>">
				        <input type="hidden" name="token" value="<?=$token?>">
						<input type="hidden" name="nonce" value="<?=$_SESSION['login_nonce']?>"/>

						<input type="password" class="large full" name="password" placeholder="New Password..." autofocus />
						<input type="password" class="large full" name="password_confirm" placeholder="New Password Confirmation..." />

						<div class="wrap">
							<div class="col xl-3-12">
								<a href="<?=site_url('login')?>" style="line-height: 90px;"><i class="fa fa-chevron-left"></i> Back to Sign In</a>
							</div>
							<div class="col xl-9-12 xl-right">
								<input type="submit" class="large" name="reset-password-submit" value="Update Password" class="user-submit" />
							</div>
						</div>
						<input type="hidden" name="redirect_to" value="<?=htmlspecialchars_decode(get('redirect'))?>" />
					</div>
				</div>
			</form><br/>

		</div>

	</div>
	<div class="dark-side">

		<div class="side-content">
			<div class="wrap">
				<div class="col xl-1-12"></div>
				<div class="col xl-11-12 xl-center">

					<a href="<?=site_url()?>" rel="home">
						<img src="<?=asset_url('images/revisionary-icon.svg')?>" alt="" class="revisionary-icon"/><br><br>
						<h2><div><b>REVISIONARY APP</b></div></h2>
					</a>

				</div>
			</div>
		</div>

	</div>
</section>

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>