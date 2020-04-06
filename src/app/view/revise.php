<?php require view('static/header_html'); ?>

<script>

	user_ID = <?=currentUserID()?>;
	user_first_name = '<?=$User->getInfo('user_first_name')?>';
	user_last_name = '<?=$User->getInfo('user_last_name')?>';
	user_email = '<?=$User->getInfo('user_email')?>';
	user_picture = '<?=$User->getInfo('user_picture')?>';
	trial_active = <?=getUserInfo()['trialActive'] ? "true" : "false"?>;
	user_level_ID = <?=getUserInfo()['trialActive'] ? getUserInfoDB()['trial_user_level_ID'] : $User->getInfo('user_level_ID')?>;
	trial_active = <?=getUserInfo()['trialActive'] ? "true" : "false"?>;
	project_ID = <?=$project_ID?>;
	page_ID = <?=$page_ID?>;
	phase_ID = <?=$phase_ID?>;
	device_ID = <?=$device_ID?>;
	screen_ID = <?=$screen_ID?>;
	remote_URL = '<?=$phaseData->remoteUrl?>';
	page_type = '<?=$page_type?>';

</script>



<div id="loading" class="overlay">

	<div class="progress-info">
		<ul>
			<li style="color: white;"><?=$phaseData->cachedUrl?></li>
		</ul>
	</div>


	<span class="loading-text">
		<div class="gps_ring"></div> <span id="loading-info">LOADING...</span>
	</span>


	<span class="dates">

		<?php
		$date_created = timeago($page['page_created'] );
		$last_updated = timeago($page['page_modified'] );

		echo "<div class='date created'><b>Date Created:</b> $date_created</div>";
		if ($date_created != $last_updated)
			echo "<div class='date updated'><b>Last Updated:</b> $last_updated</div>";
		?>

	</span>
</div>

<div id="wait" class="overlay" style="display: none;">

	<span class="loading-text">
		<div class="gps_ring"></div> <span id="loading-info">PLEASE WAIT...</span>
	</span>

</div>

<div class="bg-overlay modals">

	<?php

		// Global Modals
		require view('modules/modals-global');

		$dataType = "page";
		require view('modules/modals');

	?>

</div>

<div class="alerts hidden">

	<div id="no-pin" class="alert error autoclose hidden">
		Pin does not exist anymore.
		<a href="#" class="close"><i class="fa fa-times"></i></a>
	</div>

</div>



<?php require view('modules/revise-topbar'); ?>



<div id="page" class="site" data-user-level-id="<?=getUserInfo()['trialActive'] ? getUserInfoDB()['trial_user_level_ID'] : $User->getInfo('user_level_ID')?>">


	<div class="iframe-container" style="width: <?=$width?>px; height: <?=$height?>px;">

		<iframe sandbox="allow-same-origin allow-scripts" id="the-page" name="the-page" src="" scrolling="auto" width="<?=$width?>" height="<?=$height?>" style="min-width: <?=$width?>px; min-height: <?=$height?>px;"></iframe>

		<!-- THE PINS LIST -->
		<div id="pins" data-filter="<?=$pin_filter?>"></div>

		<!-- THE PIN CURSOR -->
		<pin class="mouse-cursor big" data-pin-type="live">1</pin>

	</div>


	<?php require view('modules/pin-window'); ?>


</div> <!-- #page.site -->

<div class="progress-bar"></div>


<?php if ( get('new') === "" ) { ?>

	<div class="ask-showing-correctly">
		<div class="inner">

			<a href="#" class="close-pop" data-popup="close"><i class="fa fa-times"></i></a>

			<p>Is this page showing correctly now?</p>

			<div class="answers">
				<a href="#" class="button good" data-popup="close"><i class="fa fa-thumbs-up"></i> Yes</a>
				<a href="<?=site_url('revise/'.$device_ID.'?redownload&ssr&secondtry')?>" class="button bad"><i class="fa fa-thumbs-down"></i> No</a>
			</div>

		</div>
	</div>

<?php } ?>

<?php if ( get('secondtry') === "" ) { ?>

	<div class="ask-showing-correctly">
		<div class="inner">

			<a href="#" class="close-pop" data-popup="close"><i class="fa fa-times"></i></a>

			<p>Is that fixed now?</p>

			<div class="answers">
				<a href="#" class="button good" data-popup="close"><i class="fa fa-smile"></i> Looks good now!</a><br/>
				<a href="<?=site_url('revise/'.$device_ID.'?redownload')?>" class="button middle"><i class="fa fa-meh"></i> Previous was better</a><br/>
				<a href="<?=site_url('revise/'.$device_ID.'?capture')?>" class="button bad"><i class="fa fa-frown"></i> Both are bad</a>
			</div>

		</div>
	</div>

<?php } ?>



<script>
$(function(){


	var loadingProcessID = newProcess(false, "loadingProcess");
	checkPageStatus(
		<?=$device_ID?>,
		<?=is_numeric($queue_ID) ? $queue_ID : "''"?>,
		<?=is_numeric($process_ID) ? $process_ID : "''"?>,
		loadingProcessID,
		"<?=$phase_type?>"
	);


});
</script>

<?php require view('static/footer_html'); ?>