<?php

use \DrewM\MailChimp\MailChimp;


if (
	isset($_POST['nonce']) && isset($_POST['email']) &&
	$_SESSION["js_nonce"] == $_POST['nonce'] &&
	filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
) {


	$MailChimp = new MailChimp('cf46d8fcb49efd7ebbe51f02aec3ec58-us17');
	$list_id = '74f14f8baf';
	$email = $_POST['email'];

	$result = $MailChimp->post("lists/$list_id/members", [
		'email_address' => $email,
		'status'        => 'subscribed',
	]);

	if ( $MailChimp->success() ) {

		if (isset($_POST['betatester']) && $_POST['betatester'] == "yes") {

			$list_id_beta = "5f5221d0cf";

			$result = $MailChimp->post("lists/$list_id_beta/members", [
				'email_address' => $email,
				'status'        => 'subscribed',
			]);

		}

		echo "<pre>";
		print_r($result);
		echo "</pre>";

		header('Location: '.$_SERVER['HTTP_REFERER'].'?subscribed');
		exit;

	} else {

		if (isset($_POST['betatester']) && $_POST['betatester'] == "yes") {

			$result = $MailChimp->post("lists/5f5221d0cf/members", [
				'email_address' => $email,
				'status'        => 'subscribed',
			]);

		}

		echo $MailChimp->getLastError()."<br>";

/*
		echo "<pre>";
		print_r( $MailChimp->getLastResponse() );
		echo "</pre>";
*/

		header('Location: '.$_SERVER['HTTP_REFERER'].'?alreadysubscribed');
		exit;
	}


}


require view('static/header_html');
?>

<div id="page">
	<main>
		<div class="vertical-middle" style="text-align: center;">
			<img src="<?=asset_url('images/revisionary-logo.png')?>" width="400" height="90" alt="Revisionary Logo"/>
			<h1>We are coming soon</h1>
			<p>Would you like to be notified when we're ready?</p>


		<?php
		if ( isset($_GET['alreadysubscribed']) ) {
		?>
			<div class="xl-center" style="color: green; font-size: 30px;">You are already subscribed, we'll let you know. Thank you!</div>
		<?php
		} elseif ( isset($_GET['subscribed']) ) {
		?>
			<div class="xl-center" style="color: green; font-size: 30px;">You are now subscribed, we'll let you know when we're ready. <br/>Thank you!</div>
		<?php
		} else {
		?>
			<form action="" method="post">

				<div class="wrap xl-center container">
					<div class="col xl-1-3">
						<input type="email" name="email" class="large full" placeholder="Please enter your email address.." required/>
					</div>
					<div class="col">
						<input type="submit" value="Notify Me!" class="large full" style="margin-left: -10px;"/>
					</div>
				</div><br/>

				<label class="xl-hiddenn"><input type="checkbox" name="betatester" value="yes"/> <small>I would like to be a beta tester as well</small></label>

				<input type="hidden" name="nonce" value="<?=$_SESSION["js_nonce"]?>"/>
			</form>
		<?php
		}
		?>
		</div>

	</main>
</div><!-- #page -->

<?php require view('static/footer_html'); ?>