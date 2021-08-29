<?php require view('static/header_html'); ?>
	<div id="page" class="site">

<section class="decorative right-dark">
	<div class="light-side">

		<div class="side-content">

			<?php

			if ( isset($_GET['sent']) ) {

				echo "We've sent you a password reset link that you can set a new password. Please check your inbox. The link will expire in one hour.";

			} elseif ( isset($_GET['error']) ) {

				echo "We had some trouble resetting your password. <br><b>Please try again later.</b>";

			} else {

			?>

			<h2>LOST PASSWORD</h2>

			<?php

				foreach ($errors as $error)
					echo $error."<br>";

				if (count($errors) > 0) echo "<br>";
			?>

			<form method="post" action="">
				<div class="wrap">
					<div class="col xl-11-12">
						<input type="hidden" name="nonce" value="<?=$_SESSION['login_nonce']?>"/>
						<input type="text" class="large full" name="email" value="<?=$userName?>" size="20" id="user_login" placeholder="Usename or Email Address…" autofocus />

						<div class="wrap">
							<div class="col xl-3-12">
								<a href="<?=site_url('login')?>" style="line-height: 90px;"><i class="fa fa-chevron-left"></i> Back to Sign In</a>
							</div>
							<div class="col xl-9-12 xl-right">
								<input type="submit" class="large" name="lost-password-submit" value="Send Reset Link" class="user-submit" />
							</div>
						</div>
						<input type="hidden" name="redirect_to" value="<?=htmlspecialchars_decode(get('redirect'))?>" />
					</div>
				</div>
			</form><br/>

			<?php

			}

			?>


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