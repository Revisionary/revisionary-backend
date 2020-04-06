<?php require view('static/header_html'); ?>
	<div id="page" class="site">

<section class="decorative right-dark">
	<div class="light-side">

		<div class="side-content">
			<h2>SIGN UP <div style="font-size: 32px;">TO MAKE THE WEB BETTER PLACE</div></h2>

			<?php
				foreach ($errors as $error)
					echo $error."<br>";

				if (count($errors) > 0) echo "<br>";
			?>

			<form method="post" action="">
				<div class="wrap">
					<div class="col xl-11-12">
						<input type="hidden" name="nonce" value="<?=$_SESSION['signup_nonce']?>"/>
						<input type="email" class="large full" name="email" value="<?=$eMail?>" size="20" placeholder="Email Address…" autofocus />
						<input type="text" class="large full" name="full_name" value="<?=$fullName?>" size="20" placeholder="First and Last Name…" />
						<input type="password" class="large full" name="password" value="" size="20" placeholder="Password…" />

						<div class="wrap xl-gutter-8">
							<div class="col xl-6-12 xl-right">
								<span class="register-message">Don't hesitate, it's <strong>free!</strong></span>
							</div>
							<div class="col xl-6-12">
								<input type="submit" name="user-submit" value="Register" class="user-submit large full" />
							</div>
						</div>

						<input type="hidden" name="redirect_to" value="<?=!empty(get('redirect')) ? htmlspecialchars_decode(get('redirect')) : site_url('projects')?>" />
						<input type="hidden" name="trial" value="<?=!empty(get('trial')) ? get('trial') : ""?>" />
					</div>
				</div>
			</form><br/>


			<div class="wrap register-button">
				<div class="col xl-11-12">
					<div class="xl-center button-desc">If you are already registered</div>
					<a href="<?=site_url('login'.( !empty(get('redirect')) ? "?redirect=".urlencode(get('redirect')) : "" ) )?>"><button class="large full">LOGIN</button></a>
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