<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="container">

		<div class="xl-center">
			<h1>CHOOSE YOUR PLAN</h1>
			<p>Find the features you'll need to make your clients happy.</p>
		</div>

		<?php require view('modules/pricing-table'); ?>

		<div class="faq xl-center">
		
			<p><small>For more info, please visit our <a href="<?=site_url('faq')?>#"><b><u>FAQ</u></b></a> page.</small></p>

		</div><br>

	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>