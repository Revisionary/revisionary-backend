<div class="toolbar wrap xl-flexbox xl-middle">
	<div class="col xl-3-12 xl-left tasks">

		<span class="title"><b><?=$dataType == "page" ? "Project Status:" : "Tasks Status:"?></b></span>


		<span class="pin-count-wrapper">
			<span class="count-title" data-count="<?=$inCompletePinsCount?>">Incomplete</span>
			<span class="pin-count middle remaining" data-count="<?=$inCompletePinsCount?>"><?=$inCompletePinsCount?></span>
			<div class="pin-stats">

				<pin data-pin-type="content" data-count="<?=$contentPinsCount?>"><?=$contentPinsCount?>
					<div class="pin-title">Content</div>
				</pin>

				<pin data-pin-type="style" data-count="<?=$stylePinsCount?>"><?=$stylePinsCount?>
					<div class="pin-title">Style</div>
				</pin>

				<pin data-pin-type="comment" data-count="<?=$commentPinsCount?>"><?=$commentPinsCount?>
					<div class="pin-title">Comment</div>
				</pin>
			
			</div>
		</span>


		<span class="pin-count-wrapper">
			<span class="count-title" data-count="<?=$completePinsCount?>">Solved</span>
			<span class="pin-count middle done" data-count="<?=$completePinsCount?>"><?=$completePinsCount?></span>
			<div class="pin-stats">

				<pin class="show-number" data-pin-type="content" data-pin-complete="1" data-count="<?=$completeContentPinsCount?>"><?=$completeContentPinsCount?>
					<div class="pin-title">Content</div>
				</pin>

				<pin class="show-number" data-pin-type="style" data-pin-complete="1" data-count="<?=$completeStylePinsCount?>"><?=$completeStylePinsCount?>
					<div class="pin-title">Style</div>
				</pin>

				<pin class="show-number" data-pin-type="comment" data-pin-complete="1" data-count="<?=$completeCommentPinsCount?>"><?=$completeCommentPinsCount?>
					<div class="pin-title">Comment</div>
				</pin>
			
			</div>
		</span>



		<?=$inCompletePinsCount + $completePinsCount == 0 ? "<span class='no-task'>No Tasks Added Yet</span>" : ""?>

	</div>

	<div class="col xl-6-12 xl-center filter invert-hover">

		<div class="filter-blocks">
			<input type="text" placeholder="Type here..." class="search-in-page"/>
			<i class="fa fa-search" data-tooltip="Search in <?=$dataType?>s"></i>
		</div>

	<?php

		$url_prefix = "projects";
		$cat_type = "project";

		// If "Pages" page
		if ($_url[0] == "project") {

			$url_prefix = "project/".$_url[1];
			$cat_type = $_url[1];

		}

	?>
		<a class="<?=$catFilter == "" ? "selected" : ""?>" href="<?=site_url($url_prefix)?>">All</a>
		<a class="<?=$catFilter == "mine" ? "selected" : ""?>" href="<?=site_url($url_prefix.'/mine')?>">Mine</a>
		<a class="<?=$catFilter == "shared" ? "selected" : ""?>" href="<?=site_url($url_prefix.'/shared')?>">Shared With Me</a>


	<?php
		$allMyCategories = $dataType == "project" ? $User->getProjectCategories() : $User->getPageCategories($project_ID);
		foreach ($allMyCategories as $catLink) {

			// Skip the Uncategorized category
			if ($catLink['cat_ID'] == 0) continue;


			echo '<a class="item '.($catFilter == permalink($catLink['cat_name']) ? "selected" : "").'" href="'.site_url( $url_prefix.'/'.permalink($catLink['cat_name']) ).'" data-type="'.$dataType.'category" data-id="'.$catLink['cat_ID'].'"><span class="name">'.$catLink['cat_name'].'</span></a>';

		}


		$action_url = 'ajax?type=data-action&data-type='.$dataType.'category&id=new&nonce='.$_SESSION['js_nonce'];

		if ($dataType == "page")
			$action_url .= '&firstParameter='.$project_ID;

	?>
		<a href="<?=site_url($action_url.'&action=addNew')?>" class="action" data-actionn="addNew" data-tooltip="Add New Category"><span style="font-family: Arial; font-weight: bold;"><i class="fa fa-plus"></i></span></a>

	</div>

	<div class="col xl-3-12 xl-right inline-guys">

		<?php
			// If pages shown
			if ($_url[0] == "project") {
		?>
		<div class="dropdown click-to-open choose-to-close">
			<a href="#">SCREEN <i class="fa fa-caret-down" aria-hidden="true"></i></a>
			<ul class="selectable xl-left screen-selector">

				<li <?= $screenFilter == "" || $screenFilter == "all" ? ' class="selected"' : ""?>>
					<a href="<?=current_url('', 'screen')?>" data-screen="5"><i class="fa" aria-hidden="true"></i> All</a>
				</li>

			<?php
			foreach($available_screens as $screen) {
			?>

				<li <?=$screenFilter == $screen['screen_cat_ID'] ? ' class="selected"' : ""?>>
					<a href="<?=current_url('screen='.$screen['screen_cat_ID'])?>" data-screen="4"><i class="fa <?=$screen['screen_cat_icon']?>" aria-hidden="true"></i> <?=$screen['screen_cat_name']?></a>
				</li>

			<?php
			}
			?>
			</ul>
		</div>
		<?php
			}
		?>

		<div class="dropdown click-to-open choose-to-close" style="margin-left: 15px;">
			<a href="#">COLUMN <i class="fa fa-caret-down" aria-hidden="true"></i></a>
			<ul class="selectable xl-left size-selector">
				<li class="selected"><a href="#" data-column="6">6 Column</a></li>
				<li><a href="#" data-column="5">5 Column</a></li>
				<li><a href="#" data-column="4">4 Column</a></li>
				<li><a href="#" data-column="3">3 Column</a></li>
				<li><a href="#" data-column="2">2 Column</a></li>
			</ul>
		</div>

		<div class="dropdown click-to-open choose-to-close" style="margin-left: 15px;">
			<a href="#">SORT <i class="fa fa-caret-down" aria-hidden="true"></i></a>
			<ul class="selectable xl-left order-selector">
				<li <?=!isset($_GET['order']) || get('order') == "custom" ? ' class="selected"' : ""?>>
					<a href="<?=current_url('', 'order')?>" data-order="custom">Custom</a>
				</li>

				<li <?=get('order') == "name" ? ' class="selected"' : ""?>><a href="<?=current_url('order=name')?>" data-order="name">By Name</a></li>
				<li <?=get('order') == "date" ? ' class="selected"' : ""?>><a href="<?=current_url('order=date')?>" data-order="date">By Date</a></li>
			</ul>
		</div>


		<?php
			// If projects shown
			if ($_url[0] == "projects") {
		?>
		<div class="dropdown click-to-open" style="margin-left: 15px;">
			<a href="#">TOOLS <i class="fa fa-caret-down" aria-hidden="true"></i></a>
			<ul class="xl-left tool-selector">
				<li data-tooltip="Coming Soon..."><a href="#">Github Integration</a></li>
				<li data-tooltip="Coming Soon..."><a href="#">BitBucket Integration</a></li>
				<li data-tooltip="Coming Soon..."><a href="#">Asana Integration</a></li>
				<li data-tooltip="Coming Soon..."><a href="#">Trello Integration</a></li>
				<li data-tooltip="Coming Soon..."><a href="#">More Apps...</a></li>

			</ul>
		</div>
		<?php
			}
		?>


	</div>
</div>