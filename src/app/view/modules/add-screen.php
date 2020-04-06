<ul class="xl-left screen-adder">


	<?php if ($screensPercentage >= 100) { ?>
	<li><a href="<?=site_url("upgrade")?>" class="add-phase"><i class="fa fa-exclamation-circle"></i> <b>Increase Screen Limit Now</b></a></li>
	<?php } ?>


	<?php
	foreach ($User->getScreenData() as $screen_cat_ID => $screen_cat) {
		if ($screensPercentage >= 100) break;

		$first_screen = reset($screen_cat['screens']);

	?>

	<li class="screen-cat" data-screen-cat-id="<?=$screen_cat_ID?>">

		<a href="#" class="has-sub" data-first-screen-id="<?=$first_screen['screen_ID']?>">
			<i class="fa <?=$screen_cat['screen_cat_icon']?>" aria-hidden="true"></i> <?=$screen_cat['screen_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i> <i class="fa fa-file-upload"></i>
		</a>
		<ul class="addable xl-left screen-add">
			<?php
			foreach ($screen_cat['screens'] as $screen) {

				$screen_link = site_url("projects/?new_screen=".$screen['screen_ID']."&phase_ID=".$blockPhase['phase_ID']);
				$screen_label = $screen['screen_name']." (".$screen['screen_width']."x".$screen['screen_height'].")";
				if ($screen['screen_ID'] == 11) {
					$screen_link = queryArg('page_width='.$screen['screen_width'], $screen_link);
					$screen_link = queryArg('page_height='.$screen['screen_height'], $screen_link);
					$screen_label = "Current Window (<span class='screen-width'>".$screen['screen_width']."</span>x<span class='screen-height'>".$screen['screen_height']."</span>)";
				}

				//$screen_link = queryArg('nonce='.$_SESSION["new_screen_nonce"], $screen_link);
			?>
			<li class="screen">
				<a href="<?=$screen_link?>"
					class="new-screen"
					data-screen-id="<?=$screen['screen_ID']?>"
					data-screen-width="<?=$screen['screen_width']?>"
					data-screen-height="<?=$screen['screen_height']?>"
					data-screen-cat-name="<?=$screen_cat['screen_cat_name']?>"
					data-screen-cat-icon="<?=$screen_cat['screen_cat_icon']?>"
				>
					<span><?=$screen_label?></span>
				</a>
			</li>
			<?php
			}

			// Custom Screen
			if ($screen_cat['screen_cat_name'] == "Custom...") {
			?>
			<li><a href="#" data-screen-id="<?=$screen['screen_ID']?>">Add New</a></li>
			<?php
			}
			?>
		</ul>

	</li>

	<?php
	}
	?>
</ul>