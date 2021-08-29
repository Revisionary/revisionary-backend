<?php require view('static/header_html'); ?>
	<div id="page" class="site">

<section class="decorative right-dark">
	<div class="light-side">

		<div class="side-content">
			<h2>SIGN IN <div>TO CHANGE THE WEB</div></h2>

			<?php
				foreach ($errors as $error)
					echo $error."<br>";

				if (count($errors) > 0) echo "<br>";
			?>

			<form method="post" action="">
				<div class="wrap">
					<div class="col xl-11-12">
						<input type="hidden" name="nonce" value="<?=$_SESSION['login_nonce']?>"/>
						<input type="text" class="large full" name="username" value="<?=$userName?>" size="20" id="user_login" placeholder="Usename or Email Address…" autofocus />
						<input type="password" class="large full" name="password" value="" size="20" id="user_pass" placeholder="Password…" />

						<div class="wrap">
							<div class="col xl-7-12">
								<a href="<?=site_url('lost-password')?>" style="line-height: 90px;">Forgot password?</a>
							</div>
							<div class="col xl-5-12 xl-right">
								<input type="submit" class="large" name="login-submit" value="Login" class="user-submit" />
							</div>
						</div>
						<input type="hidden" name="redirect_to" value="<?=htmlspecialchars_decode(get('redirect'))?>" />
					</div>
				</div>
			</form><br/>


			<div class="wrap register-button">
				<div class="col xl-11-12">
					<div class="xl-center button-desc">If you aren't registered yet</div>
					<a href="<?=site_url('signup'.( !empty(get('redirect')) ? "?redirect=".urlencode(get('redirect')) : "" ) )?>"><button class="large full">REGISTER FOR FREE</button></a>
				</div>
			</div>

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