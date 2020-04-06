<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="container">

		<div class="xl-center"><br>
			<h1>Frequently Asked Questions</h1><br>
		</div>


		<div class="wrap xl-2 xl-gutter-40 faqs">
			<div class="col faq" id="load-limit">

				<h2 class="h5">What is the load limit?</h2>
				<p>Load limit determines your all page's downloaded file sizes. Only your static HTML, JS, and CSS files count in that limit.</p>

			</div>
			<div class="col faq" id="downloads">

				<h2 class="h5">What does Revisionary store?</h2>
				<p>Every time you add a page/project, or create a phase, Revisionary App downloads three main assets from your URL for you to save its current state: Static HTML, JS and CSS files. The other assets like images or videos won't be downloaded. So, you can browse on each stage of your webpage.</p>

			</div>
		</div>

		<div class="wrap xl-2 xl-gutter-40 faqs">
			<div class="col faq" id="projects">

				<h2 class="h5">What are the projects?</h2>
				<p>Projects are the group of your website's pages. Each project represents a website in your account.</p>

			</div>
			<div class="col faq" id="pages">

				<h2 class="h5">What are the pages?</h2>
				<p>Pages are the URLs you added to the Revisionary App.</p>

			</div>
		</div>

		<div class="wrap xl-2 xl-gutter-40 faqs">
			<div class="col faq" id="phases">

				<h2 class="h5">What are the phases?</h2>
				<p>Phases are the exact state of your page URLs from the time they created. Once you create a new page or project, Revisionary creates an initial phase to serve you the current state of your URL. So, you can do your changes or add your comments on it. Once all the tasks are applied on the URL, you can create a new phase to give new feedback on it, or to confirm the updates. </p>

			</div>
			<div class="col faq" id="screens">

				<h2 class="h5">What are the devices & screens?</h2>
				<p>You can add different screen sizes to your pages(actually for the phases of your page) to see the page in different screen resolutions. So, you can put your comments or do your changes on mobile devices. Initially, Revisionary creates your pages with the current browser window size while you're adding. Then, you can create new predefined device sizes to your page, or you can resize your browser window and add that size by adding a new "Current Window" screen.</p>

			</div>
		</div>

		<div class="wrap xl-2 xl-gutter-40 faqs">
			<div class="col faq" id="pins">

				<h2 class="h5">What are the pins?</h2>
				<p>Pins are the needles that you can put onto anything on your page to change any content like images, paragraphs, button labels, and all of their styles, and/or to create feedback conversations about them.</p>

			</div>
			<div class="col faq" id="js-sites">

				<h2 class="h5">Does it work with JS websites like React or Vue JS?</h2>
				<p>Yes, Revisionary downloads JS website's server side rendered version. So, you can use Revisionary App for your single page web apps or Vue/React based websites.</p>

			</div>
		</div>

		<div class="wrap xl-2 xl-gutter-40 faqs">
			<div class="col faq" id="free-plan">

				<h2 class="h5">What can I do with the Free plan?</h2>
				<p>You can do almost everthing with the Free plan. But, there are some limitations on amount of Projects, Pages, Screens, Pins, and the load size of your webpage.</p>

			</div>
			<div class="col faq">

				<h2 class="h5">Any other questions?</h2>
				<p>For more information, please feel free to contact us at <a href="mailto:info@revisionaryapp.com" class="text-color-secondary">info@revisionaryapp.com</a></p>

			</div>
		</div>


		<br><br>
	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>