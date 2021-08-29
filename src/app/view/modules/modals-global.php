<div id="video" class="popup-window xl-center xl-5-12">
	<a href="#" class="cancel-button" style="position: absolute; right: 20px; top: 20px;"><i class="fa fa-times"></i></a>

	<h2 style="margin-bottom: 0;">Quick Start</h2>
	<p style="margin-top: 0;">In development...</p>


	<iframe width="560" height="315" data-src="https://www.youtube.com/embed/a3ICNMQW7Ok?autoplay=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="max-width: 100%;"></iframe>


</div>





<div id="notifyme" class="popup-window xl-center xl-6-12 lg-11-12">
	<a href="#" class="cancel-button" style="position: absolute; right: 20px; top: 20px;"><i class="fa fa-times"></i></a>


	<div class="xl-center">
		<h3 style="margin-top: 0;"><b>Almost There</b></h3>
	</div>

	<div class="wrap xl-1 xl-center">
		<div class="col">
		
			<p>We'll let you know when this feature available on <b style="display: inline-block;">Revisionary App</b>.</p><br>

			<form action="" method="post" id="notify-me-form" data-status="">
				<div class="wrap xl-table xl-gutter-16">
					<div class="col form-field">
						<input type="email" name="notify-email" value="<?=userLoggedIn() ? getUserInfo()['email'] : ""?>" class="full" placeholder="Enter your email address..." style="font-size: 18px; height: 39px !important;">
						<input type="hidden" name="feature" value="all">
					</div>
					<div class="col xl-4-12 form-field">
						<button class="dark small full">Notify Me</button>
					</div>
					<div class="col xl-center result already-exists">

						<p><b>You've already subscribed for this email address. Thanks again!</b></p>

					</div>
					<div class="col xl-center result success">

						<p><b>Thank you for your interest! We'll let you know as soon as this feature is available.</b></p>

					</div>
				</div>
			</form>

		</div>
	</div>


</div>