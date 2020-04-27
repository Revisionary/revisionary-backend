<?php

$user_levels = $db->get('user_levels');
//die_to_print($user_levels);


// Delete the admin one
unset($user_levels[0]);
//die_to_print($user_levels);


// Current user info
$current_user_info = getUserInfo();
$current_user_info_DB = getUserInfoDB();
$current_user_level_name = @$current_user_info['userLevelName'];
$trial_user_level_name = @$current_user_info['trialAvailable'] ? @$current_user_info_DB['trial_user_level_name'] : "";


$isExceed = false;
if ( 
	@$projectsPercentage >= 100 || 
	@$phasesPercentage >= 100 || 
	@$screensPercentage >= 100 || 
	@$pinsPercentage >= 100 || 
	@$commentPinsPercentage >= 100 || 
	@$loadPercentage >= 100
)
	$isExceed = true;

?>

<table class="pricing">
	<tr>
		<th class="feature"><span>Features</span></th>
		<?php foreach ($user_levels as $user_level) { ?>
		<th class="align-center"><?=$user_level['user_level_name']?></th>
		<?php } ?>
	</tr>
	<tr>
		<td>Max Load <a href="<?=site_url('faq#load-limit')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center <?=$loadPercentage >= 100 && $current_user_level_name == $user_level['user_level_name'] ? "exceed" : ""?>">
			<?php if (
				$current_user_level_name == $user_level['user_level_name'] || 
				$trial_user_level_name == $user_level['user_level_name'] 
			) { ?>
				<span class="current tooltip" data-tooltip="Current Load"><?=$loadCount?>MB /</span>
			<?php } ?>
			<?=$user_level['user_level_max_load'] == 2048 ? "2GB" : $user_level['user_level_max_load']."MB"?>
		</td>
		<?php } ?>
	</tr>
	<tr>
		<td># of Projects <a href="<?=site_url('faq#projects')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center <?=$projectsPercentage >= 100 && $current_user_level_name == $user_level['user_level_name'] ? "exceed" : ""?>">
			<?php if (
				$current_user_level_name == $user_level['user_level_name'] || 
				$trial_user_level_name == $user_level['user_level_name'] 
			) { ?>
				<span class="current tooltip" data-tooltip="Current Project Count"><?=$projectsCount?> /</span>
			<?php } ?>
			<?=$user_level['user_level_max_project'] == 99999 ? "Unlimited" : $user_level['user_level_max_project']?>
		</td>
		<?php } ?>
	</tr>
	<tr>
		<td># of Pages & Phases & Design Uploads <a href="<?=site_url('faq#phases')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center <?=$phasesPercentage >= 100 && $current_user_level_name == $user_level['user_level_name'] ? "exceed" : ""?>">
			<?php if (
				$current_user_level_name == $user_level['user_level_name'] || 
				$trial_user_level_name == $user_level['user_level_name'] 
			) { ?>
				<span class="current tooltip" data-tooltip="Current Page/Phase Count"><?=$phasesCount?> /</span>
			<?php } ?>
			<?=$user_level['user_level_max_page'] == 99999 ? "Unlimited" : $user_level['user_level_max_page']?>
		</td>
		<?php } ?>
	</tr>
	<tr>
		<td># of Screens <a href="<?=site_url('faq#screens')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center <?=$screensPercentage >= 100 && $current_user_level_name == $user_level['user_level_name'] ? "exceed" : ""?>">
			<?php if (
				$current_user_level_name == $user_level['user_level_name'] || 
				$trial_user_level_name == $user_level['user_level_name'] 
			) { ?>
				<span class="current tooltip" data-tooltip="Current Screen Count"><?=$screensCount?> /</span>
			<?php } ?>
			<?=$user_level['user_level_max_screen'] == 99999 ? "Unlimited" : $user_level['user_level_max_screen']?>
		</td>
		<?php } ?>
	</tr>
	<tr>
		<td># of Live Content & Style Pins <a href="<?=site_url('#live-change')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center <?=$pinsPercentage >= 100 && $current_user_level_name == $user_level['user_level_name'] ? "exceed" : ""?>">
			<?php if (
				$current_user_level_name == $user_level['user_level_name'] || 
				$trial_user_level_name == $user_level['user_level_name'] 
			) { ?>
				<span class="current tooltip" data-tooltip="Current Live Pins Count"><?=$pinsCount?> /</span>
			<?php } ?>
			<?=$user_level['user_level_max_live_pin'] == 99999 ? "Unlimited" : $user_level['user_level_max_live_pin']?>
		</td>
		<?php } ?>
	</tr>
	<tr>
		<td># of Only Comment Pins <a href="<?=site_url('faq#pins')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center <?=$commentPinsPercentage >= 100 && $current_user_level_name == $user_level['user_level_name'] ? "exceed" : ""?>">
			<?php if (
				$current_user_level_name == $user_level['user_level_name'] || 
				$trial_user_level_name == $user_level['user_level_name'] 
			) { ?>
				<span class="current tooltip" data-tooltip="Current Comment Pins Count"><?=$commentPinsCount?> /</span>
			<?php } ?>
			<?=$user_level['user_level_max_comment_pin'] == 99999 ? "Unlimited" : $user_level['user_level_max_comment_pin']?>
		</td>
		<?php } ?>
	</tr>
	<tr>
		<td># of User to Share <a href="<?=site_url('faq')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<td class="align-center">20</td>
		<td class="align-center">Unlimited</td>
		<td class="align-center">Unlimited</td>
	</tr>
	<tr>
		<td>Comment on Design Files <a href="<?=site_url('#design-feedback')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<td class="align-center">Yes</td>
		<td class="align-center">Yes</td>
		<td class="align-center">Yes</td>
	</tr>
	<tr>
		<td>See Content Differences <a href="<?=site_url('#content-diff')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<td class="align-center">-</td>
		<td class="align-center">Yes</td>
		<td class="align-center">Yes</td>
	</tr>
	<tr>
		<td>FreeHand Draw (Coming Soon) <a href="<?=site_url('#draw')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<td class="align-center">-</td>
		<td class="align-center">Yes</td>
		<td class="align-center">Yes</td>
	</tr>
	<tr>
		<td>Integrations (Coming Soon) <a href="<?=site_url('#integrations')?>" target="_blank" data-tooltip="Click for more info"><i class="fas fa-question-circle"></i></a></td>
		<td class="align-center">-</td>
		<td class="align-center">-</td>
		<td class="align-center">Yes</td>
	</tr>


	<tr valign="top">
		<td></td>
		<?php foreach ($user_levels as $user_level) { ?>
		<td class="align-center">
			$<?=$user_level['user_level_price']?><span><?=$user_level['user_level_price'] != 0 ? "/m" : ""?></span><br><br>
			<?php

			if ($page_title == "Pricing") { // If not logged in


				if ($user_level['user_level_name'] == "Free") {

					echo "<a href='".site_url('signup')."' class='upgrade-button'>Get Started</a>";

					echo "<div class='try'>Absolutely free</div>";

				} else {

					echo "<a href='".site_url('signup?trial='.$user_level['user_level_name'])."' class='upgrade-button'>Try ".$user_level['user_level_name']."</a>";

					echo "<div class='try'>No card needed</div>";

				}


			} else { // If logged in


				if ( $current_user_level_name == $user_level['user_level_name'] ) {


					echo "<b class='current-plan ".($isExceed ? "exceed" : "")."'>Your Current Plan</b>";


				} elseif ( $current_user_level_name != 'Free' && $user_level['user_level_name'] != 'Enterprise' ) {


					echo "<a href='#' class='cancel-plan' data-tooltip='In development...'>Downgrade to ".$user_level['user_level_name']."</a>";


				} elseif ( $current_user_level_name != $user_level['user_level_name'] ) {


					echo "<a href='#' class='upgrade-button ".($user_level['user_level_name'] == "Free" ? "invisible" : "")."' data-modal='payment'>Upgrade to ".strtoupper($user_level['user_level_name'])."</a>";


					if ( $current_user_info['trialAvailable'] ) {
	

						$left_day = $current_user_info['trialAvailableDays'];


						if ( $current_user_info_DB['trial_user_level_name'] != $user_level['user_level_name'] && $left_day > 0 ) {
						
							echo "<a href='".site_url('projects?trial='.$user_level['user_level_name'])."' class='try link'>or Try for $left_day day".($left_day > 1 ? "s" : "")."</a>";
						
						} elseif ( $current_user_info_DB['trial_user_level_name'] == $user_level['user_level_name'] ) {
						
							echo "<div class='try'>";
	
								echo "<b>You are trialing this plan</b>";
								if ($left_day > 1) echo "<div style='color: red;'>$left_day days left</div>";
								elseif ($left_day == 1) echo "<div style='color: red;'>$left_day day left</div>";
								elseif ($left_day == 0) echo "<div style='color: red;'>Last day of trial</div>";
	
							echo "</div>";
						
						}


					}


				}


			}

			?>
		</td>
		<?php } ?>
	</tr>
</table>