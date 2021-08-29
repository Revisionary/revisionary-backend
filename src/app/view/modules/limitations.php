<?php

$current_plan_name = getUserInfo()['userLevelName'];
if ( getUserInfo()['trialActive'] ) {

	$current_plan_name = getUserInfoDB()['trial_user_level_name']." (Trial)";

	$now = new DateTime();
	$later = new DateTime( getUserInfo()['trialExpireDate'] );
	$left_day = $later->diff($now)->d;
	if (getUserInfoDB()['trial_started_for'] == null) $left_day = 7;


	$left_text = "($left_day days left)";
	if ($left_day == 1) $left_text = "($left_day day left)";
	elseif ($left_day == 0) $left_text = "(Last day of trial)";

}


?>

<div class="limit-wrapper" data-current-plan="<?=getUserInfo()['userLevelName']?>">

	<div class="wrap xl-3 xl-right xl-flexbox xl-between">
		<div class="col xl-1-1 xl-right">
		
			<b><?=$current_plan_name?></b> Account Usage <?=getUserInfo()['trialActive'] ? "<small>$left_text</small>" : ""?><br>
		
		</div>
		<div class="col total projects-limit <?=$projectsPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" data-modal="upgrade" class="dropdown-opener">
				<span class='current'><?=$projectsCount?></span>/<span class='max'><?=$maxProjects?></span>
				<span class='desc'>Project<?=$maxProjects > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li>
					<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="button"><?=getUserInfo()['trialActive'] ? "UPGRADE NOW" : "<i class='fas fa-plus'></i> INCREASE PROJECT LIMIT NOW"?></a>
				</li>
			</ul>

		</div>
		<div class="col total pages-limit phases-limit <?=$phasesPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" data-modal="upgrade" class="dropdown-opener">
				<span class='current'><?=$phasesCount?></span>/<span class='max'><?=$maxPhases?></span>
				<span class='desc'>Page<?=$maxPhases > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li>
					<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="button"><?=getUserInfo()['trialActive'] ? "UPGRADE NOW" : "<i class='fas fa-plus'></i> INCREASE PAGE/PHASE LIMIT NOW"?></a>
				</li>
			</ul>

		</div>
		<div class="col total screens-limit devices-limit <?=$screensPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" data-modal="upgrade" class="dropdown-opener">
				<span class='current'><?=$screensCount?></span>/<span class='max'><?=$maxScreens?></span>
				<span class='desc'>Screen<?=$maxScreens > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li>
					<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="button"><?=getUserInfo()['trialActive'] ? "UPGRADE NOW" : "<i class='fas fa-plus'></i> INCREASE SCREEN LIMIT NOW"?></a>
				</li>
			</ul>

		</div>
		<div class="col total content-pins-limit <?=$pinsPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" data-modal="upgrade" class="dropdown-opener">
				<span class='current'><?=$pinsCount?></span>/<span class='max'><?=$maxPins?></span>
				<span class='desc'>Content Pin<?=$maxPins > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li>
					<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="button"><?=getUserInfo()['trialActive'] ? "UPGRADE NOW" : "<i class='fas fa-plus'></i> INCREASE LIVE PIN LIMIT NOW"?></a>
				</li>
			</ul>

		</div>
		<div class="col total comment-pins-limit <?=$commentPinsPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" data-modal="upgrade" class="dropdown-opener">
				<span class='current'><?=$commentPinsCount?></span>/<span class='max'><?=$maxCommentPins?></span>
				<span class='desc'>Comment Pin<?=$maxCommentPins > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li>
					<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="button"><?=getUserInfo()['trialActive'] ? "UPGRADE NOW" : "<i class='fas fa-plus'></i> INCREASE COMMENT PIN LIMIT NOW"?></a>
				</li>
			</ul>

		</div>
		<div class="col total load-limit <?=$loadPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" data-modal="upgrade" class="dropdown-opener">
				<span class='current'><?=$loadCount?></span>/<span class='max'><?=$maxLoad?></span>
				<span class='desc'>MB File<?=$maxLoad > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li>
					<a href="<?=site_url('upgrade')?>" data-modal="upgrade" class="button"><?=getUserInfo()['trialActive'] ? "UPGRADE NOW" : "<i class='fas fa-plus'></i> INCREASE SIZE LIMIT NOW"?></a>
				</li>
			</ul>

		</div>
	</div>

</div>