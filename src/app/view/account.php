<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="alerts">

		<?php if ( isset($_GET['error']) && $_GET['error'] == "name" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Please enter your full name.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "unknown" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Sorry, your information couldn't be updated.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "invalidname" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Only letters and white space allowed on your name.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "password" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Your current password is not correct.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "email" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Email address you entered is being used by another account.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "passconfirm" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Password confirmation is not same with the new password you entered.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['error']) && $_GET['error'] == "fieldsempty" ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('error');</script>
			Please don't leave fields empty.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>





		<?php } elseif ( isset($_GET['successful']) ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('successful');</script>
			Your information has been updated.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['password-changed']) ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('password-changed');</script>
			Your password successfully changed.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } ?>


	</div>

	<div id="content" class="wrap xl-1 container">
		<div class="col">


			<!-- Title -->
			<div class="wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

					<a href="<?=site_url('projects')?>" class="invert-hover" style="letter-spacing: 2.5px;"><i class="fa fa-arrow-left"></i> PROJECTS</a>

				</div>

				<div class="col xl-6-12 xl-center title">

					<h1>My Account</h1><br/><br/><br/>

				</div>

				<div class="col xl-3-12 xl-right">

					<?php require view('modules/limitations'); ?>

				</div>
			</div>


			<!-- Filter Bar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

				</div>

				<div class="col xl-6-12 xl-center filter invert-hover">

					<a class="<?=$subpage == "profile" || !$subpage ? "selected" : ""?>" href="<?=site_url("account")?>">Profile</a>
					<a class="<?=$subpage == "password" ? "selected" : ""?>" href="<?=site_url("account/password")?>">Password</a>
					<a class="<?=$subpage == "email" ? "selected" : ""?>" href="<?=site_url("account/email")?>">Email</a>
					<a class="<?=$subpage == "billing" ? "selected" : ""?>" href="<?=site_url("account/billing")?>">Billing</a>

				</div>

				<div class="col xl-3-12 xl-right inline-guys">


				</div>
			</div>


			<!-- Blocks -->
			<div class="wrap xl-center">
				<div class="col xl-6-12">


				<?php if ($subpage == "profile" || !$subpage) { ?>


					<div class="wrap xl-table xl-gutter-24" id="profile-settings">
						<div class="col xl-3-12 xl-center">

							<form id="avatar-form" action="" method="POST" enctype="multipart/form-data">
								<picture class="profile-picture larger avatar-changer" <?=$userInfo['printPicture']?> data-type="user" data-id="<?=$user_ID?>">
									<span><?=$userInfo['nameAbbr']?></span>
									<input type="file" name="image" class="avatar-upload" id="filePhoto" accept=".gif,.jpg,.jpeg,.png" data-max-size="3145728">
								</picture>
							</form>

							<br/>

							<div class="level-wrapper" data-tooltip="You are on <?=$current_plan_name == "Enterprise" || $current_plan_name == "Enterprise (Trial)" ? 'Un' : ""?>limited <?=$current_plan_name?> Plan">
								<a href="#" class="user-level" data-modal="upgrade">
									<?=$current_plan_name == "Enterprise" ? '<i class="fa fa-award"></i> ' : ""?>
									<?=$current_plan_name?>
								</a>
								<?php if ( $current_plan_name != "Enterprise" ) { ?>
									<a href="<?=site_url('upgrade')?>" data-modal="upgrade">Upgrade Now</a>
								<?php } ?>
								
							</div>

						</div>
						<form method="post" action="" class="col">


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Name</span>
								<span class="col"><input class="full" type="text" name="user_full_name" value="<?=$userInfo['fullName']?>" placeholder="Your full name"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Job Title</span>
								<span class="col"><input class="full" type="text" name="user_job_title" value="<?=$userInfoDB['user_job_title']?>" placeholder="Your job title"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Department</span>
								<span class="col"><input class="full" type="text" name="user_department" value="<?=$userInfoDB['user_department']?>" placeholder="Your department"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Company</span>
								<span class="col"><input class="full" type="text" name="user_company" value="<?=$userInfoDB['user_company']?>" placeholder="Your company name"/></span>
							</label><br/>


							<div class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12"></span>
								<span class="col"><input class="invert" type="submit" name="update-submit" value="Update"/></span>
							</div><br/>


						</form>
					</div>

				<?php } elseif ($subpage == "password") { ?>


					<div class="wrap xl-table xl-gutter-24n" id="password-settings">
						<div class="col xl-3-12 xl-center">

						</div>
						<form method="post" action="" class="col">


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Current Password</span>
								<span class="col"><input class="full" type="password" name="current_password" placeholder="Enter your current password"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">New Password</span>
								<span class="col"><input class="full" type="password" name="new_password" placeholder="Enter your new password"/></span>
							</label><br/>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">New Password Confirmation</span>
								<span class="col"><input class="full" type="password" name="new_password_confirmation" placeholder="Retype your new password"/></span>
							</label><br/>



							<div class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12"></span>
								<span class="col"><input class="invert" type="submit" name="password-submit" value="Update"/></span>
							</div><br/>


						</form>
					</div>

				<?php } elseif ($subpage == "email") { ?>


					<div class="wrap xl-table xl-gutter-24n" id="email-settings">
						<div class="col xl-3-12 xl-center">

						</div>
						<form method="post" action="" class="col">


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Email Address</span>
								<span class="col"><input class="full" type="email" name="user_email" value="<?=$userInfo['email']?>" placeholder="Enter your new password"/></span>
							</label><br/>


							<script>
								$(function() {

									var emailInput = $('input[name="user_email"]');
									var registeredVal = emailInput.val();

									emailInput.on('keyup', function() {

										var newVal = emailInput.val();

										if (registeredVal != newVal)
											$('.current-password').fadeIn();
										else
											$('.current-password').fadeOut();

									});

								});
							</script>


							<div class="current-password xl-hidden">
								<label class="wrap xl-table xl-middle xl-gutter-8">
									<span class="col xl-3-12">Current Password</span>
									<span class="col"><input class="full" type="password" name="current_password" placeholder="Enter your current password"/></span>
								</label><br/>
							</div>


							<label class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12">Email Notifications</span>
								<span class="col"><input class="full" type="checkbox" name="user_email_notifications" value="yes" <?=$userInfoDB['user_email_notifications'] ? "checked" : ""?>/> Yes, I want to receive email notifications.</span>
							</label><br/>



							<div class="wrap xl-table xl-middle xl-gutter-8">
								<span class="col xl-3-12"></span>
								<span class="col"><input class="invert" type="submit" name="email-submit" value="Update"/></span>
							</div><br/>


						</form>
					</div>


				<?php } elseif ($subpage == "billing") { ?>


					<div class="wrap xl-table xl-gutter-24n" id="billing-settings">
						<div class="col xl-3-12 xl-center">

						</div>
						<div class="col">


							<h2>Your Plan</h2>
							<p>You are currently on <b><?=$current_plan_name == "Enterprise" || $current_plan_name == "Enterprise (Trial)" ? 'Un' : ""?>limited <?=$current_plan_name?> Plan</b></p>

							<div class="wrap xl-flexbox xl-middle xl-gutter-24">
								<div class="col <?=$current_plan_name == "Enterprise" ? "xl-hidden" : ""?>">
								
									<a href="<?=site_url('upgrade')?>" class="upgrade-button" data-modal="upgrade">UPGRADE NOW</a>
								
								</div>
								<div class="col">
								
									<?php if ( $userInfo['trialActive'] ) { ?>
										<a href="<?=site_url('projects?canceltrial')?>" class="cancel" data-confirm="Are you sure you want to cancel your <?=$current_plan_name?> plan?"><small>Cancel Trial</small></a>
									<?php } elseif ( $userInfo['userLevelID'] > 2 ) { ?>
										<a href="<?=site_url('upgrade?downgrade')?>" data-modal="upgrade" class="cancel" data-tooltip="In development..."><small>Downgrade</small></a>
									<?php } ?>
								
								</div>
							</div>
							<hr>


							<h2>Payment Method</h2>
							<?php if ( $current_plan_name == "Free" ) { ?>

								<p>You are currently on Free plan.</p>

							<?php } elseif ( $userInfo['trialActive'] ) { ?>

								<a href="#" class="upgrade-button invert">Add a Card to Upgrade</a>

							<?php } elseif ( $userInfo['userLevelID'] > 2 ) { ?>

								CARD INFO HERE

							<?php } ?>
							<hr>


							<h2>Invoices</h2>
							<p>You haven't made any purchases yet.</p>


						</div>
					</div>



				<?php } ?>



				</div>
			</div>




		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>