<?php

	$title = "projects";
	$url_prefix = "projects";


	// If PAGES page
	if ($_url[0] == "project") {

		$title = "pages";
		$url_prefix = "project/".$_url[1];

	}

?>


<div class="wrap xl-flexbox xl-middle">
	<div class="col xl-3-12 xl-left">
		<?php
		if ($title == "pages" || ($title == "projects" && ($catFilter == "archived" || $catFilter == "deleted"))) {

			$goBackLink = $catFilter == "archived" || $catFilter == "deleted" ? site_url($url_prefix) : site_url('projects');

		?>

		<!-- Return to projects -->
		<a href="<?=$goBackLink?>" class="invert-hover" style="letter-spacing: 2.5px;"><i class="fa fa-arrow-left"></i> PROJECT<?=$catFilter == "archived" || $catFilter == "deleted" ? "" : "S"?><?=$title == "projects" ? "S" : ""?></a>

		<?php
		}
		?>

	</div>

	<div class="col xl-6-12 xl-center title item" <?=isset($project_ID) ? "data-id='$project_ID' data-type='project'" : ""?>>
		<div class="dropdown click-to-open" style="display: inline-block;">
			<?php if ($catFilter == "archived") { ?>
				<div class="xl-left">Archived <?=$dataType == "page" ? ucfirst($title) : ""?></div>
			<?php } elseif ($catFilter == "deleted") { ?>
				<div class="xl-left">Deleted <?=$dataType == "page" ? ucfirst($title) : ""?></div>
			<?php } ?>
			<a href="#" class="<?=$title == "pages" ? 'name-field' : ''?>" style="display: block; margin-bottom: 20px;">
				<h1 class="<?=$title == "pages" ? 'project-title' : ''?>" <?=$title == "pages" ? 'data-id="'.$project_ID.'"' : ''?>>
					<span class="name" <?=isset($project_ID) ? "data-id='$project_ID' data-type='project'" : ""?>><?=$title == "pages" ? $projectInfo['project_name'] : strtoupper($title)?></span><i class="fa fa-caret-down" style="font-size: 30px; transform: translateY(-10px);"></i>

					<?php
						if ($dataType == "page") {
					?>
					<span class="actions">
						<input class="edit-name" type="text" value="<?=$projectInfo['project_name']?>" data-type="project" data-id="<?=$project_ID?>"/>
						<span class="project-renamer" data-action="rename" data-type="project" data-id="<?=$project_ID?>"><i class="fa fa-pencil"></i></span>
					</span>
					<?php
						}
					?>
				</h1>
			</a>
			<ul class="right xl-left selectable">
				<li class="menu-item <?=$catFilter == "" ? "selected" : ""?>">
					<a href="<?=site_url($url_prefix)?>">
						<i class="fa fa-th"></i>
						ALL <?=strtoupper($title)?>
					</a>
				</li>
				<li class="menu-item <?=$catFilter == "archived" ? "selected" : ""?>">
					<a href="<?=site_url($url_prefix.'/archived')?>">
						<i class="fa fa-archive"></i>
						ARCHIVED <?=strtoupper($title)?>
					</a>
				</li>
				<li class="menu-item <?=$catFilter == "deleted" ? "selected" : ""?>">
					<a href="<?=site_url($url_prefix.'/deleted')?>">
						<i class="fa fa-trash"></i>
						DELETED <?=strtoupper($title)?>
					</a>
				</li>
			</ul>
		</div>

		<div class="wrap xl-center xl-flexbox xl-middle xl-gutter-8 under-main-title <?=$title == "projects" ? "public-link" : ""?>" <?=$title == "projects" ? "data-tooltip='Coming Soon...'" : ""?>>

			<?php if ($title == "projects") { /*?>
			<a href="<?=site_url(getUserInfo()['userName'])?>">
				<i class="fa fa-link"></i> https://revisionaryapp.com/<?=getUserInfo()['userName']?>
			</a>
			<a href="#" class="privacy">
				<i class="fa fa-globe-americas"></i> <i class="fa fa-caret-down"></i>
			</a>
			<?php */ } ?>
			<?php if ($dataType == "page") { ?>

			<div class="col">

				<a href="#" data-modal="share" data-type="project" data-id="<?=$project_ID?>" data-object-name="<?=$projectInfo['project_name']?>" data-iamowner="<?=$projectInfo['user_ID'] == currentUserID() ? "yes" : "no"?>" data-tooltip="Share this project" class="upgrade-button invert">
					<i class="fa fa-share-alt"></i> SHARE
				</a>

			</div>
			<div class="col" data-type="project" data-id="<?=$project_ID?>">

				<div class="wrap xl-flexbox">

					<?php
					$project_user_ID = $projectInfo['user_ID'];
					$project_user = getUserInfo($project_user_ID);
					?>

					<!-- Owner -->
					<picture class="col profile-picture" <?=$project_user['printPicture']?>
						data-tooltip="<?=$project_user['fullName']?>"
						data-mstatus="owner"
						data-fullname="<?=$project_user['fullName']?>"
						data-nameabbr="<?=$project_user['nameAbbr']?>"
						data-email="<?=$project_user['email']?>"
						data-avatar="<?=$project_user['userPicUrl']?>"
						data-userid="<?=$project_user_ID?>"
						data-unremoveable="unremoveable"
					>
						<span><?=$project_user['nameAbbr']?></span>
					</picture>


					<?php
					foreach ($projectShares as $share) {


						// Don't show the shared people who I didn't share to and whom shared by a person I didn't share with. :O
						if (
							$project_user_ID == currentUserID() &&
							$share['sharer_user_ID'] != currentUserID()
						) {

							$projectSharedToSharer = array_search($share['sharer_user_ID'], array_column($projectShares, 'share_to'));
							if ( $projectSharedToSharer === false ) continue;

						}

					?>

						<?php
							$shared_user_ID = $share['share_to'];

							if ( is_numeric($share['share_to']) ) {

								$shared_user_ID = intval($shared_user_ID);
								$shared_user = getUserInfo($shared_user_ID);
						?>

					<picture class="col profile-picture" <?=$shared_user['printPicture']?>
						data-tooltip="<?=$shared_user['fullName']?>"
						data-mstatus="user"
						data-fullname="<?=$shared_user['fullName']?>"
						data-nameabbr="<?=$shared_user['nameAbbr']?>"
						data-email="<?=$shared_user['email']?>"
						data-avatar="<?=$shared_user['userPicUrl']?>"
						data-userid="<?=$shared_user_ID?>"
						data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
					>
	                    <span><?=$shared_user['nameAbbr']?></span>
					</picture>
						<?php

							} else {

						?>
					<picture class="col profile-picture email"
						data-tooltip="<?=$shared_user_ID?>"
						data-mstatus="email"
						data-fullname=""
						data-nameabbr=""
						data-email="<?=$shared_user_ID?>"
						data-avatar=""
						data-userid="<?=$shared_user_ID?>"
						data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
					>
						<i class="fa fa-envelope"></i>
					</picture>

						<?php

							}

						?>

					<?php
					}
					?>

				</div>

			</div>

			<?php } // If dataType page ?>
		</div>

	</div>

	<div class="col xl-3-12 xl-right">

		<?php //require view('modules/limitations'); ?>

	</div>
</div>