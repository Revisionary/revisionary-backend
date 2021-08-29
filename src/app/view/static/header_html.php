<!DOCTYPE html>
<html class="no-js">
	<head>

		<script>document.getElementsByTagName("HTML")[0].classList.remove("no-js");</script>

		<meta charset="utf-8">
		<title><?=$page_title?></title>

		<link rel="stylesheet" href="<?=asset_url_nocache('styles/style.css')?>" media="screen">

		<?php

			// Generate new nonce for JS
			$_SESSION["js_nonce"] = uniqid(mt_rand(), true);


			// Generate new nonce for adding projects or pages
			$_SESSION["add_new_nonce"] = uniqid(mt_rand(), true);



			// Additional CSS Files
			if (isset($additionalCSS) && is_array($additionalCSS) ) {

				foreach ($additionalCSS as $file) {
					echo '<link rel="stylesheet" href="'.asset_url_nocache('styles/'.$file).'" media="screen">';
				}

			}

		?>

		<script src="<?=asset_url('scripts/vendor/jquery-3.4.1.min.js')?>"></script>
		<script>
			var ajax_url = '<?=site_url('ajax')?>';
			var nonce = '<?=$_SESSION["js_nonce"]?>';


			var loggedIn = <?=userLoggedIn() ? "true" : "false"?>;
			var trialStarted = "no";
			var trialExpired = "yes";
			var trialExpiredNotified = "yes";
			<?php if ( userLoggedIn() ) { ?>
				var user_ID = <?=currentUserID()?>;
				<?=isset($project_ID) ? "var project_ID = ".$project_ID.";" : ""?>
				<?=isset($dataType) ? "var dataType = '".$dataType."';" : ""?>
				trialStarted = '<?=getUserInfo()['trialStartedFor'] ? "yes" : "no"?>';
				trialExpired = <?=getUserInfo()['trialExpired']?>;
				trialExpiredNotified = <?=getUserInfo()['trialExpiredNotified']?>;
			<?php } ?>


			var limitations = {
				'max' : {},
				'current' : {}
			};

			<?=isset($maxProjects) ? "limitations.max.project = '".$maxProjects."';" : ""?>
			<?=isset($maxPhases) ? "limitations.max.page = '".$maxPhases."';" : ""?>
			<?=isset($maxPhases) ? "limitations.max.phase = '".$maxPhases."';" : ""?>
			<?=isset($maxScreens) ? "limitations.max.screen = '".$maxScreens."';" : ""?>
			<?=isset($maxPins) ? "limitations.max.contentpin = '".$maxPins."';" : ""?>
			<?=isset($maxCommentPins) ? "limitations.max.commentpin = '".$maxCommentPins."';" : ""?>
			<?=isset($maxLoad) ? "limitations.max.load = '".$maxLoad."';" : ""?>

			<?=isset($maxProjects) ? "limitations.current.project = '".$projectsLeft."';" : ""?>
			<?=isset($maxPhases) ? "limitations.current.page = '".$phasesLeft."';" : ""?>
			<?=isset($maxPhases) ? "limitations.current.phase = '".$phasesLeft."';" : ""?>
			<?=isset($maxScreens) ? "limitations.current.screen = '".$screensLeft."';" : ""?>
			<?=isset($maxPins) ? "limitations.current.contentpin = '".$pinsLeft."';" : ""?>
			<?=isset($maxCommentPins) ? "limitations.current.commentpin = '".$commentPinsLeft."';" : ""?>
			<?=isset($maxLoad) ? "limitations.current.load = '".$loadLeft."';" : ""?>

			<?php
			if ( isset($pages) ) {

				echo "var myPages = [];";

				foreach ($pages as $myPage) {

					// Skip archived and deleted
					if ($myPage['page_deleted'] || $myPage['page_archived'] || $myPage['project_deleted'] || $myPage['project_archived']) continue;
			?>

			myPages.push({
				page_url : '<?=$myPage['page_url']?>',
				page_ID : <?=$myPage['page_ID']?>,
				page_name : '<?=$myPage['page_name']?>',
				project_ID : <?=$myPage['project_ID']?>,
				project_name : '<?=$myPage['project_name']?>'
			});

			<?php
				}

			}
			?>
		</script>

		<?php

			// Additional JS Files
			if (isset($additionalHeadJS) && is_array($additionalHeadJS) ) {

				foreach ($additionalHeadJS as $file) {

					if ($file == "vendor/tinymce.min.js") {
						echo "<script src='https://cdn.tiny.cloud/1/5b6i6yoeijf0rxszlhqd8cif1vdgtqalh2cxyhjso3kz4yhz/tinymce/5/tinymce.min.js' referrerpolicy='origin'></script>";
						continue;
					}

					echo '<script src="'.asset_url_nocache('scripts/'.$file).'"></script>';
				}

			}
		?>

		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="32x32">
		<link rel="icon" href="<?=asset_url('images/revisionary-icon.png')?>" sizes="192x192">
		<link rel="apple-touch-icon-precomposed" href="<?=asset_url('images/revisionary-icon.png')?>">
		<meta name="msapplication-TileImage" content="<?=asset_url('images/revisionary-icon.png')?>">

	</head>
	<body class="<?=isset($_url[0]) ? $_url[0] : ""?>">
