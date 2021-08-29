	<footer>
		<div class="wrap xl-2 container xl-flexbox xl-middle">
			<div class="col">
				<div class="site-title"><a href="<?=site_url()?>" rel="home">REVISIONARY APP</a></div>
				<div class="copyright">Copyright &copy; <?=date('Y')?> - <?=$_parsed_current_url['subdomain']?></div>
			</div>
			<div class="col xl-right footer-links">
				<nav style="margin-bottom: 10px;">
					<a href="<?=userLoggedIn() ? "/help/get-started" : site_url('signup')?>">Get Started</a> | 
					<a href="<?=site_url('#features')?>">Features</a> | 
					<a href="<?=site_url('#use-cases')?>">Use Cases</a> | 
					<a href="<?=site_url('pricing')?>">Pricing</a> | 
					<a href="<?=site_url('faq')?>">FAQ</a>
				</nav>
				<div class="social">

					<a href="https://www.linkedin.com/company/revisionaryapp/" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
					<a href="https://twitter.com/RevisionaryApp" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
					<a href="https://www.facebook.com/RevisionaryApp/" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
					<a href="https://www.instagram.com/AppRevisionary/" target="_blank"><i class="fab fa-instagram-square"></i></a>
					<a href="https://www.youtube.com/channel/UCb_wJjNMOthbCeawrSkIA9w" target="_blank"><i class="fa fa-youtube-square" aria-hidden="true"></i></a>

				</div>
			</div>
		</div>
	</footer><!-- .site-footer -->


	<div class="bg-overlay modals">

		<?php

			// Global Modals
			require view('modules/modals-global');

			// Projects and Pages Modals
			if ( userLoggedIn() ) require view('modules/modals');

		?>

	</div>


</div><!-- #page.site -->