<div id="page" class="site">

	<div class="progress-bar"></div>

	<main id="first-section">

		<header class="wrap xl-flexbox xl-middle xl-between xl-outside-60 lg-2">

			<div class="col branding-side xl-left">

				<a href="<?php echo site_url(); ?>" rel="home" class="logo">
					<img src="<?=asset_url('images/revisionary-app-logo.png')?>" srcset="<?=asset_url('images/revisionary-app-logo.png')?> 1x, <?=asset_url('images/revisionary-app-logo@2x.png')?> 2x" alt="Revisionary Logo" width="405" height="70" />
					<span class="version bottom-tooltip" data-tooltip="Updated <?=timeago( $config['last_update'] )?>">ALPHA</span>
				</a>

			</div><!-- .branding-side -->

			<div class="col menu-side xl-right inline-guys">

				<?php
				if ( !userLoggedIn() ) {
				?>

				<nav id="main-navigation">

					<ul>
						<li class="menu-features" style="margin-right: 35px;"><a href="<?=site_url('#features')?>">Features</a></li>
						<li class="menu-use-cases"><a href="<?=site_url('#use-cases')?>">Use Cases</a></li>
						<li class="login-link"><a href="<?=site_url('login')?>">Login</a></li>
						<li class="signup-link"><a href="<?=site_url('signup')?>">Sign Up</a></li>
					</ul>

				</nav><!-- #main-navigation -->

				<?php
				} else {

					$notification_count = 0;

				?>
				<nav id="main-navigation">
					<ul class="user-header-menu">
						
						<?php if ($_url[0] != "projects" && $_url[0] != "project") { ?>
						<li class="menu-features" style="margin-right: 35px;"><a href="<?=site_url('#features')?>">Features</a></li>
						<li class="menu-use-cases" style="margin-right: 25px;"><a href="<?=site_url('#use-cases')?>">Use Cases</a></li>
						<?php } ?>


						<li class="menu-item">
							
							<?php if ( getUserInfo()['userLevelName'] == "Enterprise" ) { ?>
							<span class="enterprise-badge tooltip" data-tooltip="You are on Unlimited <?=getUserInfo()['userLevelName']?> Plan"><i class="fa fa-award"></i></span>
							<?php } else { ?>
							<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="upgrade-button">UPGRADE</a>
							<?php } ?>
						
						</li>

						<?php if ($_url[0] == "projects" || $_url[0] == "project") { ?>
						<li class="dropdown click-to-open" style="margin-right: 10px;">
						
							<a href="#" style="font-size: 16px;"><i class="far fa-question-circle"></i></a>
							<ul class="xl-left">
								<?php require view('modules/help-menu'); ?>
							</ul>
							
						</li>
						<?php } ?>

						<li class="menu-item notifications-wrapper">



							<a href="#" class="notification-opener refresh-notifications" style="font-size: 20px;" data-tooltip="Notifications">

								<i class="fa fa-bell"></i>
								<div class="notif-no pulse <?=$notification_count == 0 ? "hide" : ""?>"><?=$notification_count?></div>

							</a>
							<div class="notifications">
								<ul></ul>
							</div>



						</li>
					</ul>
				</nav><!-- #main-navigation -->


				<div class="dropdown click-to-open choose-to-closee">
					<a href="#" class="invert-hover user-link bullet">
						<picture class="profile-picture big header-user" <?=getUserInfo()['printPicture']?> data-type="user" data-id="<?=currentUserID()?>">
							<span><?=getUserInfo()['nameAbbr']?></span>
						</picture> <?=getUserInfo()['fullName']?>
					</a>
					<ul class="right user-menu xl-left">
						<li class="<?=$_url[0] == "projects" ? "selected" : ""?>"><a href="<?=site_url('projects')?>"><i class="fa fa-th"></i> All Projects</a></li>
						<li class="<?=$_url[0] == "account" ? "selected" : ""?>"><a href="<?=site_url('account')?>"><i class="fa fa-user-cog"></i> My Account</a></li>
						<li data-tooltip="Coming Soon..."><a href="#"><i class="fa fa-life-ring"></i> Help</a></li>
						<li><a href="#" data-modal="feedback"><i class="fa fa-comments"></i> Feedback</a></li>
						<li><a href="<?=site_url('logout')?>"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
					</ul>
				</div>

				<?php
				}
				?>

			</div><!-- .menu-side -->

		</header>