<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="alerts">

		<?php if ( isset($_GET['noaccess']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('noaccess');</script>
			Looks like you don't have access to this project.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalidurl']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalidurl');</script>
			The URL you entered is invalid.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['addprojecterror']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('addprojecterror');</script>
			Your project couldn't be added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['addpageerror']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('addpageerror');</script>
			Your page couldn't be added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['adddeviceerror']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('adddeviceerror');</script>
			Your device couldn't be added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalid']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalid');</script>
			We couldn't find the project you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['projectdoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('projectdoesntexist');</script>
			You don't have access to this project.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invaliddevice']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invaliddevice');</script>
			We couldn't find the device you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['devicedoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('devicedoesntexist');</script>
			You don't have access to this device.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['pagedoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('pagedoesntexist');</script>
			You don't have access to this page.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalidpage']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalidpage');</script>
			We couldn't find the page you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['phasedoesntexist']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('phasedoesntexist');</script>
			You don't have access to this phase.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['invalidphase']) ) { ?>

		<div class="alert error"> <script>removeQueryArgFromCurrentUrl('invalidphase');</script>
			We couldn't find the phase you are looking for.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>





		<?php } elseif ( isset($_GET['status']) && $_GET['status'] == "successful" ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('status');</script>
			New category successfully added.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } elseif ( isset($_GET['password-changed']) ) { ?>

		<div class="alert success"> <script>removeQueryArgFromCurrentUrl('password-changed');</script>
			Your password successfully changed.

			<a href="#" class="close"><i class="fa fa-times"></i></a>
		</div>

		<?php } ?>


	</div>

	<div id="content" class="wrap xl-1 container">
		<div class="col">


			<!-- Title -->
			<?php require view('modules/title'); ?>


			<!-- Filter Bar -->
			<?php require view('modules/filter-bar'); ?>


			<!-- Blocks -->
			<ol class="wrap categories <?=$order == "" && $catFilter != "shared" ? "cat-sortable" : ""?>" data-filter="<?=$catFilter?>">

			<?php

			// THE CATEGORY LOOP
			$data_count = 0;
			$categories = $dataType == "project" ? $User->getProjectCategories($catFilter, $order) : $User->getPageCategories($project_ID, $catFilter, $order);
			foreach ($categories as $category) {


				// Category action URL
				$action_url = 'ajax?type=data-action&data-type='.$dataType.'category&nonce='.$_SESSION['js_nonce'].'&id='.$category['cat_ID'];
			?>


				<li id="<?=permalink($category['cat_name'])?>"
					class="col xl-1-1 category item"
					data-type="<?=$dataType?>category"
					data-id="<?=$category['cat_ID']?>"
					data-order="<?=$category['cat_order_number']?>"
				>


					<div class="cat-separator <?php
						if (
							$category['cat_name'] == "Uncategorized" ||
							( $catFilter != "" && $catFilter != "mine" && $catFilter != "shared" )
						) echo 'xl-hidden';
						?>"
					>
						<span class="name-field">
							<span class="name cat-handle" data-type="<?=$dataType?>category" data-id="<?=$category['cat_ID']?>"><?=$category['cat_name']?></span>
							<span class="actions">

								<input class="edit-name" type="text" value="<?=$category['cat_name']?>" data-type="<?=$dataType?>category" data-id="<?=$category['cat_ID']?>"/>
								<a href="<?=site_url($action_url.'&action=rename')?>" data-action="rename"><i class="fa fa-pencil"></i></a>

								<a href="<?=site_url($action_url.'&action=remove')?>" data-action="remove"><i class="fa fa-trash"></i></a>

							</span>
						</span>

					</div>


					<ol class="wrap dpl-xl-gutter-30 xl-6 blocks <?=$order == "" && $catFilter != "shared" ? "object-sortable" : ""?>">

					<?php


					// Block Data
					$category_ID = $category['cat_ID'];
					$blocks = $dataType == "project" ? $User->getProjects($category_ID, $catFilter, $order) : $User->getPages($project_ID, $category_ID, $catFilter, $order);
					//die_to_print($blocks);

					// THE BLOCK LOOP
					$object_count = 0;
					if ( isset($blocks) && count($blocks) > 0 ) {
						foreach ($blocks as $block) {

							$contentPinCount = $stylePinCount = $privatePinCount = $completePinCount = 0;
							$blockPinStatus = "no-tasks";


							if ($dataType == "project") {


								// Block URL
								$block_url = site_url('project/'.$block['project_ID']);


								// Default Block Image !!!
								$block_image_path = "screenshots/device-0.jpg";

								if ($block['project_image_device_ID'] != null) {


									$blockDeviceData = Device::ID( $block['project_image_device_ID'], currentUserID() );
									if ($blockDeviceData) {


										$blockDeviceInfo = $blockDeviceData->getInfo();
										$block_image_path = "screenshots/device-".$block['project_image_device_ID'].".jpg";


										// // First device Image, for the projects later than 6 March 2020
										// if ( $blockDeviceInfo['device_created'] > "2020-03-05 23:07:50" ) $block_image_path = "screenshots/device-".$block['project_image_device_ID'].".jpg";


									} elseif ($projectDevices = $User->getDevices(null, null, $block['project_ID'])) {

										$firstDeviceOfProject = end($projectDevices);
										if ( isset($firstDeviceOfProject) ) {


											$block_image_path = "screenshots/device-".$firstDeviceOfProject['device_ID'].".jpg";


											// // First device Image, for the projects later than 6 March 2020
											// if ( $firstDeviceOfProject['device_created'] > "2020-03-05 23:07:50" ) $block_image_path = "screenshots/device-".$firstDeviceOfProject['device_ID'].".jpg";


											// print_r($block_image_path);
											// print_r($firstDeviceOfProject);

										}

									}


								}

								$block_image_uri = cache."/$block_image_path";
								$block_image_url = cache_url($block_image_path);


							}


							if ($dataType == "page") {


								$blockPhases = array_values( $User->getPhases( $block['page_ID'] ) );
								$blockPhase = end($blockPhases);
								$blockPhaseID = isset($blockPhase['phase_ID']) ? $blockPhase['phase_ID'] : false;
								$blockDevices = $User->getDevices( $blockPhaseID );

								$blockPageType = $block['page_url'] == "image" ? "image" : "url";


								// Screen filter
								if ( $screenFilter != "" && is_numeric($screenFilter) ) {

									// Get all devices from the block page
									$blockDevices = $User->getDevices( null, $block['page_ID'] );

									// Extract the selected screen
									$blockDevices = array_filter($blockDevices, function ($device) use ($screenFilter) {
										return ($device['screen_cat_ID'] == $screenFilter);
									});


									if ( count($blockDevices) == 0 ) continue;

								}


								$firstDevice = reset($blockDevices);


								// First device Image !!! OLD
								$block_image_path = "screenshots/device-".$firstDevice['device_ID'].".jpg";


								// // First device Image, for the projects later than 6 March 2020
								// if ( $blockPhase['phase_created'] > "2020-03-05 23:07:50" ) $block_image_path = "screenshots/device-".$firstDevice['device_ID'].".jpg";


								$block_image_uri = cache."/$block_image_path";
								$block_image_url = cache_url($block_image_path);


								// First device URL
								$block_url = site_url('revise/'.$firstDevice['device_ID']);


							}


							// Count all the pin types
							$completePinsCount = $inCompletePinsCount = $privatePinsCount = $contentPinsCount = $stylePinsCount = $commentPinsCount = $completeContentPinsCount = $completeStylePinsCount = $completeCommentPinsCount = 0;
							if ( is_array($allMyPins) ) {

								$object_ID = $block[$dataType.'_ID'];


								$completePins = array_filter($allMyPins, function($pin) use($dataType, $object_ID) {

									return $pin[$dataType.'_ID'] == $object_ID && $pin['pin_complete'] == "1";

								});
								$completePinsCount = count($completePins);


								$inCompletePins = array_filter($allMyPins, function($pin) use($dataType, $object_ID) {

									return $pin[$dataType.'_ID'] == $object_ID && $pin['pin_complete'] == "0";

								});
								$inCompletePinsCount = count($inCompletePins);


								$privatePins = array_filter($allMyPins, function($pin) use($dataType, $object_ID) {

									return $pin[$dataType.'_ID'] == $object_ID && $pin['pin_private'] == "1" && $pin['user_ID'] == currentUserID();

								});
								$privatePinsCount = count($privatePins);



								$contentPinsCount = count(array_filter($inCompletePins, function($pin) {

									return $pin['pin_type'] == "content" && $pin['pin_private'] == "0";

								}));

								$stylePinsCount = count(array_filter($inCompletePins, function($pin) {

									return $pin['pin_type'] == "style" && $pin['pin_private'] == "0";

								}));

								$commentPinsCount = count(array_filter($inCompletePins, function($pin) {

									return $pin['pin_type'] == "comment" && $pin['pin_private'] == "0";

								}));



								$completeContentPinsCount = count(array_filter($completePins, function($pin) {

									return $pin['pin_type'] == "content" && $pin['pin_private'] == "0";

								}));

								$completeStylePinsCount = count(array_filter($completePins, function($pin) {

									return $pin['pin_type'] == "style" && $pin['pin_private'] == "0";

								}));

								$completeCommentPinsCount = count(array_filter($completePins, function($pin) {

									return $pin['pin_type'] == "comment" && $pin['pin_private'] == "0";

								}));


							}


							// Get block status
							if ($inCompletePinsCount)
								$blockPinStatus = "has-tasks";

							if ($completePinsCount && !$inCompletePinsCount)
								$blockPinStatus = "done";


							// Image style bg code
							$image_style = file_exists($block_image_uri) ? "background-image: url(".$block_image_url.");" : "";

							// Is directly shared to me
							$blockSharedMe = $block['share_to'] == currentUserID();

							// Is mine
							$blockIsMine = $block['user_ID'] == currentUserID();

					?>

						<li
							class="col block item"
							data-type="<?=$dataType?>"
							data-id="<?=$block[$dataType.'_ID']?>"
							data-order="<?=$block['order_number']?>"
							data-cat-id="<?=intval($block['cat_ID'])?>"
							data-page-type="<?=$dataType == "page" ? $blockPageType : ""?>"
							data-project-id="<?=$dataType == "page" ? $project_ID : ""?>"
							data-phase-id="<?=$dataType == "page" ? $blockPhaseID : ""?>"
							data-pin-status="<?=$blockPinStatus?>">


							<div class="box object-handle xl-center <?=empty($image_style) ? "no-thumb" : ""?>" style="<?=$image_style?>">

								<div class="wrap overlay xl-flexbox xl-top xl-between xl-5 members">
									<div class="col xl-4-12 xl-left xl-top people" data-type="<?=$dataType?>" data-id="<?=$block[$dataType.'_ID']?>">

										<!-- Owner -->
										<picture class="profile-picture" style="background-image: url(<?=getUserPicUrl($block['user_picture'], $block['user_email'])?>);"
											data-tooltip="<?=$block['user_first_name']." ".$block['user_last_name']?>"
											data-type="user"
											data-id="<?=$block['user_ID']?>"
											data-unremoveable="unremoveable"
										>
											<span><?=getUserNameAbbr($block['user_first_name'], $block['user_last_name'])?></span>
										</picture>


										<?php

										// Is shared to someone
										$isShared = false;

/*
										// SHARES QUERY
										$db->where('share_type', $dataType); // Exlude other types
										$db->where('shared_object_ID', $block[$dataType.'_ID']); // Is this block?

										// Get the data
										$blockShares = $db->connection('slave')->get('shares', null, "share_to, sharer_user_ID");


										foreach ($blockShares as $share) {

											// Don't show the shared people who I didn't share to and whom shared by a person I didn't share with. :O
											if (
												$block_user_ID == currentUserID() &&
												$share['sharer_user_ID'] != currentUserID()
											) {

												$projectSharedToSharer = array_search($share['sharer_user_ID'], array_column($blockShares, 'share_to'));
												if ( $projectSharedToSharer === false ) continue;

											}

										?>

											<?php
												$shared_user_ID = $share['share_to'];

												if ( is_numeric($share['share_to']) ) {
													$shared_user = getUserInfo($shared_user_ID);
											?>

										<picture class="profile-picture" <?=$shared_user['printPicture']?>
											data-tooltip="<?=$shared_user['fullName']?>"
											data-type="user"
											data-id="<?=$shared_user_ID?>"
											data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
										>
											<span><?=$shared_user['nameAbbr']?></span>
										</picture>

											<?php

												} else {

											?>

										<picture class="profile-picture email"
											data-tooltip="<?=$shared_user_ID?>"
											data-type="user"
											data-id="<?=$shared_user_ID?>"
											data-unremoveable="<?=$share['sharer_user_ID'] == currentUserID() ? "" : "unremoveable"?>"
										>
											<i class="fa fa-envelope"></i>
										</picture>

											<?php

												}

											?>


										<?php

											// Is shared to someone
											if ($share['sharer_user_ID'] == currentUserID())
												$isShared = true;

										}
*/
										?>

									</div>
									<div class="col xl-8-12 xl-right xl-top pins">

										<span class="pin-count remaining tooltip" data-tooltip="Incomplete Tasks" data-count="<?=$inCompletePinsCount?>"><?=$inCompletePinsCount?></span>
										<span class="pin-count done tooltip" data-tooltip="Solved Tasks" data-count="<?=$completePinsCount?>"><?=$completePinsCount?></span>
										<?=$inCompletePinsCount + $completePinsCount == 0 ? "<span class='no-task'>No Tasks</span>" : ""?>

									</div>
									<div class="col xl-1-1 xl-center <?=$dataType == "page" ? "screens" : "pages"?>" style="position: relative;">




										<?php
										if ($dataType == "project") {
										?>

											<!-- Project Link -->
											<a href="<?=$block_url?>">

											<?php

												$block_count = 0;
												if ( isset($pageCounts[$block['project_ID']]) )
													$block_count = $pageCounts[$block['project_ID']];
											?>

												<div class="page-count"><?=$block_count?> <br>Page<?=$block_count > 1 ? 's' : ''?></div>

												<i class="fa fa-search" style="font-size: 120px;"></i>
											</a>

										<?php
										}
										?>


										<?php
										if ($dataType == "page") {

											//$pageStatus = Page::ID( $block['page_ID'] )->getPageStatus(true)['status'];
										?>

											<div class="dropdown click-to-open">
												<a href="#" class="upgrade-button" style="color: black !important; background-color: white;">OPEN <i class="fa fa-caret-down"></i></a>
												<ul class="center">
												<?php

												foreach ($blockDevices as $device) {

													//$pageStatus = Page::ID($device['page_ID'])->getPageStatus(true)['status'];

													$action_url = 'ajax?type=data-action&data-type=device&nonce='.$_SESSION['js_nonce'].'&id='.$device['device_ID'];

													$screenWidth = $device['device_width'] ? $device['device_width'] : $device['screen_width'];
													$screenHeight = $device['device_height'] ? $device['device_height'] : $device['screen_height'];
												?>

													<li class="xl-left item deletable" data-type="device" data-id="<?=$device['device_ID']?>">
														<a href="<?=site_url('revise/'.$device['device_ID'])?>">
															<i class="fa <?=$device['screen_cat_icon']?>"></i> <?=$device['screen_cat_name']?> (<?=$screenWidth?>x<?=$screenHeight?>)
														</a>

														<i class="fa fa-times delete <?=count($blockDevices) > 1 ? "" : "hidden"?>" data-tooltip="Delete This Screen" data-action="remove" data-confirm="Are you sure you want to completely remove this screen? Keep in mind that no one will be able to access this device and its pins anymore!"></i>
													</li>

													<!-- <span class="item device-wrap" data-type="device" data-id="<?=$device['device_ID']?>">

														<a href="<?=site_url($action_url.'&action=remove')?>" data-tooltip="Delete This Screen" class="remove-device <?=count($blockDevices) > 1 ? "" : "hidden"?>" data-action="remove" data-confirm="Are you sure you want to completely remove this screen? Keep in mind that no one will be able to access this device and its pins anymore!"><i class="fa fa-times-circle"></i></a>
													</span> -->

												<?php
												}
												?>

													<li class="xl-left dropdown click-to-open <?=!$blockPhaseID ? "xl-hidden" : ""?>">
														<a href="#"><i class="fa fa-plus"></i> <b>Add New Screen</b></a>
														<?php require view('modules/add-screen'); ?>
													</li>
												</ul>
											</div>

										<?php
										} // if ($dataType == "page")
										?>


									</div>
									<div class="col xl-4-12 xl-left share">


										<a href="#" data-modal="share" data-type="<?=$dataType?>" data-id="<?=$block[$dataType.'_ID']?>" data-object-name="<?=$block[$dataType.'_name']?>" data-iamowner="<?=$block['user_ID'] == currentUserID() ? "yes" : "no"?>" data-tooltip="Share"><i class="fa fa-share-alt"></i></a>


									</div>
									<div class="col xl-4-12 xl-center dropdown click-to-open">

										<?php
										if ($dataType == "page") {

											//die_to_print($blockPhases, false);
											$blockPhaseNumber = array_search($blockPhase, $blockPhases) + 1;
										?>

<a href="#">v<?=$blockPhaseNumber?> <i class="fa fa-caret-down"></i></a>
<ul class="xl-left">
	<?php
	foreach($blockPhases as $otherPhase) {

		$otherPhaseNumber = array_search($otherPhase, $blockPhases) + 1;


		// Devices of the phase
		$devices_of_phase = $User->getDevices( $otherPhase['phase_ID'] );
		$firstDevice = reset($devices_of_phase);
		//die_to_print($devices_of_phase, false);


		// Get pins count
		$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($otherPhase) {

			return $pinFound['phase_ID'] == $otherPhase['phase_ID'] && $pinFound['pin_complete'] == "0";

		}));
		$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($otherPhase) {

			return $pinFound['phase_ID'] == $otherPhase['phase_ID'] && $pinFound['pin_complete'] == "1";

		}));


		// Get page status
		$pinStatus = "no-tasks";
		if ($inCompletePinCount)
			$pinStatus = "has-tasks";

		if ($completePinCount && !$inCompletePinCount)
			$pinStatus = "done";

		$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;

	?>

	<li class="item deletable <?=$blockPhase['phase_ID'] == $otherPhase['phase_ID'] ? "selected" : ""?>" data-type="phase" data-id="<?=$otherPhase['phase_ID']?>">
		<a href="<?=site_url('phase/'.$blockPhase['phase_ID'])?>"><i class="fa fa-code-branch"></i> v<?=$otherPhaseNumber?> (<?=timeago($otherPhase['phase_created'])?>)

		<span class="pin-count normal remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count normal done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span></a>

		<?php if ( count($devices_of_phase) > 1 ) { ?>
		<ul>
			<?php
			foreach ($devices_of_phase as $deviceFromPhase) {


				// Get pins count
				$inCompletePinCount = count(array_filter($allMyPins, function($pinFound) use ($deviceFromPhase) {

					return $pinFound['device_ID'] == $deviceFromPhase['device_ID'] && $pinFound['pin_complete'] == "0";

				}));
				$completePinCount = count(array_filter($allMyPins, function($pinFound) use ($deviceFromPhase) {

					return $pinFound['device_ID'] == $deviceFromPhase['device_ID'] && $pinFound['pin_complete'] == "1";

				}));


				// Get page status
				$pinStatus = "no-tasks";
				if ($inCompletePinCount)
					$pinStatus = "has-tasks";

				if ($completePinCount && !$inCompletePinCount)
					$pinStatus = "done";

				$statusCount = $pinStatus == "done" ? $completePinCount : $inCompletePinCount;


			?>
			<li class="item" data-type="device" data-id="<?=$deviceFromPhase['device_ID']?>">
				<a href="<?=site_url('revise/'.$deviceFromPhase['device_ID'])?>">
					<i class="fa <?=$deviceFromPhase['screen_cat_icon']?>"></i> <?=$deviceFromPhase['screen_cat_name'] == "Custom" ? "Custom Screen" : $deviceFromPhase['screen_cat_name']?>
					<span class="pin-count normal remaining" data-count="<?=$inCompletePinCount?>"><?=$inCompletePinCount?></span><span class="pin-count normal done" data-count="<?=$completePinCount?>"><?=$completePinCount?></span>
				</a>
			</li>
			<?php
			}
			?>
		</ul>
		<?php } ?>


		<?php if ( $blockPhase['phase_ID'] != $otherPhase['phase_ID'] ) { ?>

			<i class="fa fa-times delete" data-tooltip="Delete This Phase" data-action="remove" data-confirm="Are you sure you want to remove this phase?"></i>

		<?php } ?>

	</li>

	<?php } ?>

	<li>
		<?php if ($phasesPercentage >= 100) { ?>
		<a href="<?=site_url("upgrade")?>" class="add-phase"><i class="fa fa-exclamation-circle"></i> <b>Increase Page/Phase Limit Now</b></a>
		<?php } else { ?>
		<a href="<?=site_url("projects?new_phase=".$block['page_ID']."&page_width=1440&page_height=774")?>" class="add-phase"><i class="fa fa-plus"></i> <b>Add New Phase</b></a>
		<?php } ?>
	</li>
</ul>

										<?php } ?>

									</div>
									<div class="col xl-4-12 xl-right actions">


										<?php

											$action_url = 'ajax?type=data-action&data-type='.$dataType.'&nonce='.$_SESSION['js_nonce'].'&id='.$block[$dataType.'_ID'];

										if (
											$dataType == "project"
											|| $dataType == "page"
											|| getUserInfo()['userLevelID'] == 1
										) {

											if ($catFilter == "archived" || $catFilter == "deleted") {
										?>
<a href="<?=site_url($action_url.'&action=recover')?>" data-action="recover" data-confirm="Are you sure you want to recover this <?=$dataType?>?" data-tooltip="Recover"><i class="fa fa-reply"></i></a>
										<?php
											} else {
										?>
<a href="<?=site_url($action_url.'&action=archive')?>" data-action="archive" data-confirm="Are you sure you want to archive this <?=$dataType?>?" data-tooltip="Archive"><i class="fa fa-archive"></i></a>
										<?php
											}
										?>
<a href="<?=site_url($action_url.'&action='.($catFilter == "deleted" ? 'remove' : 'delete'))?>" data-action="<?=$catFilter == "deleted" ? 'remove' : 'delete'?>" data-confirm="<?=$catFilter == "deleted" ? 'Are you sure you want to completely remove this '.$dataType.'? Keep in mind that no one will be able to access this '.$dataType.' anymore!' : 'Are you sure you want to trash this '.$dataType.'?' ?>" data-tooltip="<?=$catFilter == "deleted" ? 'Remove' : 'Trash'?>" class="<?=!$blockIsMine && $dataType == "project" && $catFilter == "deleted" ? "xl-hidden" : ""?>"><i class="fa fa-trash"></i></a>
									<?php
										}
									?>




									</div>
								</div>

							</div>

							<div class="wrap xl-flexbox xl-top">
								<div class="col xl-8-12 xl-left box-name">


									<span class="name-field">
										<span class="share-icons"><?=$isShared ? '<i class="fa fa-share-square-o" data-tooltip="You have shared this '.$dataType.' to someone."></i>' : ""?><?=$block['user_ID'] != currentUserID() ? '<i class="fa fa-share-alt" data-tooltip="Someone has shared this '.$dataType.' to you."></i> ' : ''?></span> <a href="<?=$block_url?>" class="invert-hover name" data-type="<?=$dataType?>" data-id="<?=$block[$dataType.'_ID']?>"><?=$block[$dataType.'_name']?></a>

										<?php
										// if (
										// 	$block['user_ID'] == currentUserID()
										// 	|| $block['share_to'] == currentUserID()
										// 	|| getUserInfo()['userLevelID'] == 1
										// 	|| (isset($projectSharedMe) && $projectSharedMe)
										// ) {
										?>
										<span class="actions">

											<input class="edit-name item" type="text" value="<?=$block[$dataType.'_name']?>" data-type="<?=$dataType?>" data-id="<?=$block[$dataType.'_ID']?>" />
											<a href="<?=site_url($action_url.'&action=rename')?>" data-action="rename"><i class="fa fa-pencil"></i></a>

										</span>
										<?php
										// }
										?>

									</span>


								</div>
								<div class="col xl-4-12 xl-right date">

									<?=date("d M Y", strtotime($block[$dataType.'_created']))?>

								</div>
							</div>


						</liclass="col>

				<?php

						$data_count++;
						$object_count++;

					} // END OF THE BLOCK LOOP
				} // If defined


				// If no entry
				if ($object_count == 0) {

					$add_new_link = "";
					if (
						$catFilter != "archived"
						&& $catFilter != "deleted"
						&& $catFilter != "shared"
					) $add_new_link = "<a href='#' onClick='$(this).parents(\"li\").find(\".add-new-block .box\").addClass(\"pulse\").find(\"input[name=page-url]\").focus();'><b><u>Add one?</u></b></a>";


					echo "<li class='col xl-1-1 xl-center empty-cat' style='margin-bottom: 60px;'>
						No ".$dataType."s added here yet. $add_new_link
					</li>";

				}


				?>


					</ol><!-- .wrap -->
				</li><!-- .category -->


			<?php

			} // END OF THE CATEGORY LOOP

			?>

			</ol> <!-- .blocks -->


			<?php
			if ($dataType == "page") {
			?>


			<form action="" id="image-device-adder" class="xl-hidden">
				<input type="hidden" name="project_ID" value="">
				<input type="hidden" name="page_ID" value="">
				<input type="hidden" name="phase_ID" value="">
				<input type="hidden" name="screens[]" value="">
				<input type="file" name="design-upload" accept=".gif,.jpg,.jpeg,.png" data-max-size="15000000">
			</form>


			<div class="wrap xl-1 xl-center">
				<div class="col" style="margin-bottom: 60px;">

					<div class="date-statistics">
						<b>Created:</b> <?=timeago($projectInfo['project_created'])?><br/>
						<b>Modified:</b> <?=timeago($project_modified)?>
					</div>

				</div>
			</div>


			<?php
			}
			?>



		</div><!-- .col -->
	</div><!-- #content -->



</main><!-- .site-main -->


<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>