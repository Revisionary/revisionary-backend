<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="vertical-middle">
		<div class="enter-url">
			<div class="home-questions">
				<span class="are-you">Are you</span>
				<ul>
					<li>a Content editor</li>
					<li>an SEO Manager</li>
					<li>a Web Designer</li>
					<li>a Web Developer</li>
					<li>a Website Customer</li>
					<li>a Translator</li>
					<li>a Web Consultant</li>
				</ul>
			</div>

			<form action="<?=site_url('projects', true)?>" method="get" class="new-project-form">

				<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
				<input type="hidden" name="add_new" value="true"/>
				<input type="hidden" name="project_ID" value="autodetect"/>
				<input type="hidden" name="category" value="0"/>
				<input type="hidden" name="order" value="0"/>

				<input type="hidden" name="screens[]" value="11"/>
				<input type="hidden" name="page_width" value="1440"/>
				<input type="hidden" name="page_height" value="900"/>


				<input type="url" name="page-url" class="large full" placeholder="ENTER A WEBSITE URL" tabindex="1" required autofocust/>
				<input type="submit" title="Go Revising!"/>
			</form>
			<span class="description">Add your comments and edit any website’s content, <br/>
				for <del class="diff red">a fee</del><span class="diff green">free!</span><sup>*</sup> <br/><br/>

				<a href="#" class="button" data-tooltip='Coming soon...' data-modal="video"><i class="fa fa-play-circle"></i> See How It Works</a>

			</span>
		</div>
	</div>


	<a href="#features" class="more-info">
		<h2>Features</h2>
		<img src="<?=asset_url('icons/icon-arrow-down.svg')?>" alt=""/>
	</a>

</main><!-- #first-section -->


<section id="features" class="features">
	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-2-3">
		
			<h3><strong>Ease of Use,</strong> <br> No Download or Installation</h3>
			<p>Put your live URL and start giving feedback on any website! No need to download and install anything to your computer or browser.</p>
			<div><a href="<?=site_url('signup')?>" class="button"><i class="fas fa-rocket"></i> Get Started</a></div>

		</div>
		<div class="col xl-1-3 xl-right">
		
			<?php require view('animations/ease-of-use'); ?>
		
		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container" id="content-change">
		<div class="col xl-1-3 perspective-right" style="pointer-events: none; user-select: none;">
		
			<img src="<?=asset_url('images/content-edit.png')?>" alt="">
		
		</div>
		<div class="col xl-2-3 xl-right">
		
			<h3><strong>No Need to Write Comments,</strong> <br> Change the Live Content & Style</h3>
			<p>You don't have to wait to see the changes you want on a website until developers apply them for you. Just do it yourself and see how does it look before you send them!</p>
			<div><a href="<?=site_url('signup')?>" class="button"><i class="far fa-thumbs-up"></i> Do it Yourself</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container" id="content-diff">
		<div class="col xl-2-3">
		
			<h3>See the <strong>Content Differences</strong></h3>
			<p>Great to see the content changes on a live site. But what's changed on that big paragraph? <br> You can see the content differences easily to see what's exactly removed and added.</p>
			<div><a href="<?=site_url('signup?trial=Plus')?>" class="button"><i class="fas fa-exchange-alt"></i> Try Now</a></div>

		</div>
		<div class="col xl-1-3 xl-right perspective-left" style="pointer-events: none; user-select: none;">
		
			<img src="<?=asset_url('images/differences.png')?>" alt="">
		
		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-1-3 perspective-right" style="pointer-events: none; user-select: none;">
		
			<img src="<?=asset_url('images/devices.png')?>" alt="">
		
		</div>
		<div class="col xl-2-3 xl-right">
		
			<h3>On <strong>any Device Screen</strong></h3>
			<p>Found an issue on mobile view of a website? Or, on larger screens? Just add a new device to your page. So, you can do changes and add your comments on any device screen size.</p>
			<div><a href="<?=site_url('signup')?>" class="button"><i class="fas fa-mobile-alt"></i> See in Action</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container" id="phases">
		<div class="col xl-2-3">
		
			<h3><strong>Phases</strong></h3>
			<p>When you are done with all the changes on a page and it's been live, add a new phase to start from the new version of that page. Pull the latest changes and start revising again! And, you can see what's been done so far.</p>
			<div><a href="<?=site_url('signup')?>" class="button"><i class="fas fa-code-branch"></i> Work in Versions</a></div>

		</div>
		<div class="col xl-1-3 xl-right perspective-left" style="pointer-events: none; user-select: none;">
		
			<img src="<?=asset_url('images/versions.png')?>" alt="">
		
		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container xl-hidden">
		<div class="col xl-1-3 perspective-right">
			
			<ol>
				<li class="h1">Projects</li>
				<li class="h2">Pages</li>
				<li class="h3">Phases</li>
				<li class="h4">Devices</li>
				<li class="h5">Pins</li>
			</ol>

		</div>
		<div class="col xl-2-3 xl-right">
		
			<h3>Everything is <strong>Really Organized</strong></h3>
			<p>All the parts of the Revisionary App like Projects, Pages, Phases/Versions, Devices, Pins, Comments. Everything is grouped logically, accessibly and understandable for you. </p>
			<div><a href="<?=site_url('signup')?>" class="button"><i class="fas fa-sitemap"></i> Find & Do It Easy</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container" id="design-feedback">
		<div class="col xl-1-3 xl-right perspective-right" style="pointer-events: none; user-select: none;">
		
			<img src="<?=asset_url('images/design-comment.png')?>" alt="">
		
		</div>
		<div class="col xl-2-3 xl-right">
		
			<h3><strong>Comments on Design Files</strong> <br> Or on Any Image</h3>
			<p>You can use Revisionary even on your design process! Upload your design files and add your comments as easy as commenting on live URLs with Revisionary App.</p>
			<div><a href="<?=site_url('signup')?>" class="button"><i class="fas fa-paint-brush"></i> Give a Design Feedback</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container" id="draw">
		<div class="col xl-2-3">
		
			<h3><strong>Draw on a Website,</strong> <br> Like on a Screenshot</h3>
			<p>Draw a rectangle, circle, arrow or do a freehand drawing on a live website to show issues. <br> That will be possible on Revisionary App for the people cannot give up the screenshot habits.</p>
			<div class="wrap xl-flexbox xl-middle xl-gutter-24 action">
				<div class="col">
					Coming Soon
				</div>
				<div class="col">
					<a href="#" class="button" data-modal="notifyme"><i class="far fa-envelope"></i> Notify Me</a>
				</div>
			</div>

		</div>
		<div class="col xl-1-3 perspective-left" style="pointer-events: none; user-select: none;">
		
			<img src="<?=asset_url('images/mark.png')?>" alt="">
		
		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container" id="integrations">
		<div class="col xl-1-3 xl-center perspective-right">
			<i class="fa fa-github" style="font-size: 120px;"></i>
			<i class="fa fa-bitbucket" style="font-size: 120px;"></i>
			<i class="fa fa-trello" style="font-size: 120px;"></i>
		</div>
		<div class="col xl-2-3 xl-right">
		
			<h3>Integrations</h3>
			<p>You'll be able to automatically create GitHub or BitBucket Issues, Asana or Trello Tasks. <br> Just with one click of adding pins on any page!</p>
			<div class="wrap xl-flexbox xl-middle xl-right xl-gutter-24 action">
				<div class="col">
					<a href="#" class="button" data-modal="notifyme"><i class="far fa-envelope"></i> Notify Me</a>
				</div>
				<div class="col">
					Coming Soon
				</div>
			</div>

		</div>
	</div><br><br><br>


	<a href="#use-cases" class="more-info">
		<h2>Use Cases</h2>
		<img src="<?=asset_url('icons/icon-arrow-down.svg')?>" alt=""/>
	</a>

	<div class="container xl-right footnote">
		<p><sup>*</sup> Most of the features are free in the free plan with some limitations.</p>
	</div>

</section>



<section id="use-cases" class="decorative right-dark">
	<div class="light-side">

		<div class="side-content">
			<h3><div>Revisionary For</div> Content Management</h3>
			<p>If you are a website Content Editor, or an SEO manager, or a Translator, <b>Revisionary App</b> is best solution for you to do your changes or updates on a live site with just a few clicks.</p>

			<div class="wrap xl-flexbox xl-middle xl-gutter-24 action">
				<div class="col">

					<a href="#" class="button" data-modal="video" data-tooltip="In development..."><i class="fas fa-play-circle"></i> SHOW ME HOW</a>

				</div>
				<div class="col">
				
					<a href="<?=site_url('signup')?>"><b><u>Signup Now for Free</u></b></a>
				
				</div>
			</div>
			
		</div>

	</div>
	<div class="dark-side">

		<div class="side-content">
			<i class="fas fa-feather-alt"></i>
		</div>

	</div>
</section>


<section class="decorative left-dark">
	<div class="dark-side">

		<div class="side-content">
			<i class="fas fa-comments"></i>
		</div>

	</div>
	<div class="light-side">

		<div class="side-content">
			<h3><div>Revisionary For</div> Feedback</h3>
			<p><b>Revisionary App</b> is also best solution for getting feedback from your clients, developers or web consultants. Get your bug reports, feature requests, recommendations, design updates, text/image/style updates in one place. </p>

			<div class="wrap xl-flexbox xl-middle xl-gutter-24 action">
				<div class="col">

					<a href="#" class="button" data-modal="video" data-tooltip="In development..."><i class="fas fa-play-circle"></i> SHOW ME HOW</a>

				</div>
				<div class="col">
				
					<a href="<?=site_url('signup')?>"><b><u>Signup Now for Free</u></b></a>
				
				</div>
			</div>

		</div>

	</div>
</section>


<section class="decorative right-dark">
	<div class="light-side">

		<div class="side-content">
			<h3><div>Revisionary For</div> Developers, Designers & Freelancers</h3>
			<p>On your design or development process, expect your client feedback from <b>Revisionary App</b>, instead of getting time consuming emails, screenshots, phone calls, or meetings. <br><br>

			Love to use ThemeForest templates? Update the content and logo of your chosen templates for your clients to decide before you purchase them.</p>

			<div class="wrap xl-flexbox xl-middle xl-gutter-24 action">
				<div class="col">

					<a href="#" class="button" data-modal="video" data-tooltip="In development..."><i class="fas fa-play-circle"></i> SHOW ME HOW</a>

				</div>
				<div class="col">
				
					<a href="<?=site_url('signup')?>"><b><u>Signup Now for Free</u></b></a>
				
				</div>
			</div>

		</div>

	</div>
	<div class="dark-side">

		<div class="side-content">
			<i class="fas fa-coffee"></i>
		</div>

	</div>
</section>

<script>
jQuery(document).ready(function($) {


	// Update the current screen size !!! Common?
	$(window).resize(function() {

		var width = $(this).width();
		var height = $(this).height();

		screenWidth = width;
		screenHeight = height - 45 - 2; // -45 for the topbar, -2 for borders !!! ?

		//console.log(width, height);

		// Show new values
		$('.screen-width').text(screenWidth);
		$('.screen-height').text(screenHeight);

		// Edit the input values
		$('input[name="page_width"]').attr('value', screenWidth);
		$('input[name="page_height"]').attr('value', screenHeight);


		$('[data-screen-id="11"]').attr('data-screen-width', screenWidth);
		$('[data-screen-id="11"]').attr('data-screen-height', screenHeight);


		$('.new-screen[data-screen-id="11"]').each(function() {

			var newScreenURL = $(this).attr('href');
			var widthOnURL = getParameterByName('page_width', newScreenURL);
			var heightOnURL = getParameterByName('page_height', newScreenURL);

			var newURL = newScreenURL.replace('page_width='+widthOnURL, 'page_width='+screenWidth);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+screenHeight);

			$(this).attr('href', newURL);
			//console.log(newURL);

		});


	}).resize();


	// QUESTIONS LOOP
	$('.home-questions ul > li:first-child').addClass('active');

	var total_question = $('.home-questions ul > li').length;
	var question = 1;
	setInterval(function() {

		$('.home-questions ul > li').removeClass('active');
		$('.home-questions ul > li').eq(question%total_question).addClass('active');

		question++;

	}, 2500);


	// SMOOTH SCROLL ON ID CLICKS
	// Select all links with hashes
	$('a[href*="#"]')
		// Remove links that don't actually link to anything
		.not('[href="#"]')
		.not('[href="#0"]')
		.click(function(event) {
		// On-page links
		if (
		  location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
		  &&
		  location.hostname == this.hostname
		) {
		  // Figure out element to scroll to
		  var target = $(this.hash);
		  target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
		  // Does a scroll target exist?
		  if (target.length) {
		    // Only prevent default if animation is actually gonna happen
		    event.preventDefault();
		    $('html, body').animate({
		      scrollTop: target.offset().top
		    }, 1000, function() {
		      // Callback after animation
		      // Must change focus!
		      var $target = $(target);
		      $target.focus();
		      if ($target.is(":focus")) { // Checking if the target was focused
		        return false;
		      } else {
		        $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
		        $target.focus(); // Set focus again
		      };
		    });
		  }
		}
	});

});
</script>

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>