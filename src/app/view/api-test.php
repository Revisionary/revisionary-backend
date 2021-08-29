<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="container">

		<div class="xl-center"><br>
			<h1>API TEST</h1><br>
		</div>


		<div class="wrap xl-1 xl-gutter-40 faqs">
			<div class="col faq" id="load-limit">

				<h2 class="h5">Results</h2>
				<pre id="results"></pre>

			</div>
		</div>

<script>
<?php //User::ID()->getProjects(); ?>
// $.ajax({
//     url: '<?=site_url("v1/project")?>',
//     type: 'GET',
//     success: function(result) {
// 		console.log(result);
// 		$('#results').html( JSON.stringify(result) );
// 	},
// 	fail: function(error) {
// 		console.error(error);
// 		$('#results').html( JSON.stringify(error) );
// 	}
// });	
</script>


		<br><br>
	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>