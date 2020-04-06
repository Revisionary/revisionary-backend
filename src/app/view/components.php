<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>


	<style>
	/* TEMPORARY STYLES */

	.inputs input:hover,
	.inputs button:hover,
	.inputs .button:hover,
	.inputs .button-oval:hover {
		border-color: #007acc;
	}

	.inputs input:focus,
	.inputs button:focus,
	.inputs .button:focus,
	.inputs .button-oval:focus {
		box-shadow: 0px 0px 5px 1px #007acc;
		border-color: #007acc;
	}

	.color {
		display: inline-block;
		width: 60px;
		height: 30px;
		background-color: black;
		border: 1px solid black;
	}

	</style>

	<div class="container" id="the-page"><br>

		<h1 class="xl-center"><?=$page_title?></h1>


		<br/>
		<br/>
		<br/>
		<br/>

		<b>Color</b><br><br>
		<div class="wrap xl-gutter-40">
			<div class="col">

				<b>Primary Color </b><br/><br/>
				<div class="color" style="background-color: #000000;"></div><br>
				#000000


			</div>
			<div class="col">

				<b>Secondary Color </b><br/><br/>
				<div class="color" style="background-color: #007acc;"></div><br>
				#007acc

			</div>
			<div class="col">

				<b>Background Color </b><br/><br/>
				<div class="color" style="background-color: #ffffff;"></div><br>
				#ffffff

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-40 xl-table">
			<div class="col">

				<b>Logo </b><br/><br/>
				<img src="<?=asset_url("images/revisionary-app-logo@2x.png")?>" width="405" height="70" alt=""/>

			</div>
			<div class="col">

				<b>Icon </b><br/><br/>
				<img src="<?=asset_url("images/revisionary-icon.png")?>" width="125" height="125" alt=""/>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Typography</b><br>
		<div class="wrap xl-gutter-40">
			<div class="col">

				<h2 style="margin-top: 20px;">Roboto Condensed</h2>
				<h3 style="font-weight: 300;">Light</h3>
				<h3 style="font-weight: 300;"><i>Light Italic</i></h3>
				<h3 style="font-weight: 400;">Regular</h3>
				<h3 style="font-weight: 400;"><i>Regular Italic</i></h3>
				<h3 style="font-weight: 700;">Bold</h3>
				<h3 style="font-weight: 700;"><i>Bold Italic</i></h3>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Titles </b><br/><br/>
		<h1>Heading 1</h1>
		<h2 style="font-size: 40px;">Heading 2</h2>
		<h3>Heading 3</h3>
		<h4>Heading 4</h4>
		<h5>Heading 5</h5>
		<h6>Heading 6</h6>


		<br/>
		<br/>
		<br/>
		<br/>


		<style>
			p a {
				color: #007acc;
				text-decoration: underline;
			}

			p a:hover {
				text-decoration: none;
			}
		</style>

		<b>Paragraph</b> <br/>
		<p>Lorem ipsum dolor sit amet, <a href="#"><b>consectetur</b> adipisicing elit</a>, <b>sed do <i>eiusmod</i></b> tempor <i>incididunt ut labore et</i> dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Icons</b><br>
		<div class="wrap xl-gutter-40">
			<div class="col">


				<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>

		<b>Dropdowns </b><br/><br/>
		<div class="wrap xl-gutter-40">
			<div class="col">


				<span class="dropdown">
					<a href="#" class="learn-more">Dropdown on Link <i class="fa fa-caret-down" aria-hidden="true"></i></a>
					<ul>
						<li><span>No Link Item</span></li>
						<li><a href="#">Linked Item</a></li>
						<li class="selected"><a href="#">Selected Item</a></li>
						<li><a href="#"><i class="fa fa-user"></i> Item with Icon</a></li>
						<li class="selected"><a href="#"><i class="fa fa-user"></i> Selected Iconic Item</a></li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Alt Dropdown Item <i class="fa fa-caret-right" aria-hidden="true"></i></a>
							<ul>
								<li><span>Sub Item 1</span></li>
								<li>
									<a href="#"><i class="fa fa-sign-in-alt"></i> Sub Item 2 <i class="fa fa-caret-right" aria-hidden="true"></i></a>
									<ul>
										<li><span>Subber Item 1</span></li>
										<li>

											<a href="#"><i class="fa fa-sign-in-alt"></i> Subber Item 2 <i class="fa fa-caret-right" aria-hidden="true"></i></a>
											<ul>
												<li><span>Subber Item 1</span></li>
												<li>
													<a href="#"><i class="fa fa-sign-in-alt"></i> Subber Item 2 <i class="fa fa-caret-right" aria-hidden="true"></i></a>
													<ul>
														<li><span>Subber Item 1</span></li>
														<li><span>Subber Item 2</span></li>
														<li><span>Subber Item 3</span></li>
													</ul>
												</li>
												<li><span>Subber Item 3</span></li>
											</ul>

										</li>
										<li><span>Subber Item 3</span></li>
									</ul>
								</li>
								<li><a href="#"><i class="fa fa-sign-in-alt"></i> Sub Item 3</a></li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Selectables <i class="fa fa-caret-right" aria-hidden="true"></i></a>
							<ul class="selectable">
								<li><a href="#">Selectable 1</a></li>
								<li class="selected"><a href="#">Selectable 2</a></li>
								<li><a href="#">Selectable 3</a></li>
								<li><a href="#">Selectable 4</a></li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Addables <i class="fa fa-caret-right" aria-hidden="true"></i></a>
							<ul class="addable">
								<li><a href="#">Addable 1</a></li>
								<li><a href="#">Addable 2</a></li>
								<li><a href="#">Addable 3</a></li>
								<li><a href="#">Addable 4</a></li>
							</ul>
						</li>
					</ul>
				</span>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Text Inputs </b><br/><br/>
		<div class="wrap xl-gutter-24 inputs">
			<div class="col">

				<small>Normal</small><br/>
				<input type="text" class="large" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

				<small>Hover</small><br/>
				<input type="text" class="large" placeholder="Text Box" style="display: inline-block; border-color: #007acc;"/><br/><br/>

				<small>Focus</small><br/>
				<input type="text" class="large" placeholder="Text Box" style="display: inline-block; border-color: #007acc; box-shadow: 0px 0px 5px 1px #007acc;"/><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<input type="text" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

				<small>Hover</small><br/>
				<input type="text" placeholder="Text Box" style="display: inline-block; border-color: #007acc;"/><br/><br/>

				<small>Focus</small><br/>
				<input type="text" placeholder="Text Box" style="display: inline-block; border-color: #007acc; box-shadow: 0px 0px 5px 1px #007acc;"/><br/><br/>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Buttons </b><br/><br/>
		<div class="wrap xl-gutter-24 inputs">
			<div class="col">

				<small>Normal</small><br/>
				<button class="large"><a href="#">Large Button</a></button><br/><br/>

				<small>Hover</small><br/>
				<button class="large" style="border-color: #007acc;"><a href="#" style="color: #007acc;">Large Button</a></button><br/><br/>

				<small>Focus</small><br/>
				<button class="large" style="box-shadow: 0px 0px 5px 1px #007acc; border-color: #007acc;"><a href="#" style="color: #007acc;">Large Button</a></button><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<button><a href="#">Small Button</a></button><br/><br/>

				<small>Hover</small><br/>
				<button style="border-color: #007acc;"><a href="#" style="color: #007acc;">Small Button</a></button><br/><br/>

				<small>Focus</small><br/>
				<button style="box-shadow: 0px 0px 5px 1px #007acc; border-color: #007acc;"><a href="#" style="color: #007acc;">Small Button</a></button><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<button class="invert"><a href="#" class="invert">Invert Button</a></button><br/><br/>

				<small>Hover</small><br/>
				<button class="invert" style="border-color: #007acc; background-color: white;"><a href="#" style="color: black; border-color: black; background-color: white;">Invert Button</a></button><br/><br/>

				<small>Focus</small><br/>
				<button class="invert" style="box-shadow: 0px 0px 5px 1px #007acc; border-color: #007acc; background-color: white;"><a href="#" style="color: black; background-color: white;">Invert Button</a></button><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<a href="#" class="button-oval"><i class="fa fa-play-circle"></i> Oval Button with Icon</a><br/><br/>

				<small>Hover</small><br/>
				<a href="#" class="button-oval" style="border-color: #007acc; color: #007acc;"><i class="fa fa-play-circle"></i> Oval Button with Icon</a><br/><br/>

				<small>Focus</small><br/>
				<a href="#" class="button-oval" style="box-shadow: 0px 0px 5px 1px #007acc; border-color: #007acc; color: #007acc;"><i class="fa fa-play-circle"></i> Oval Button with Icon</a><br/><br/>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>

		<b>Forms </b><br/><br/>

		<div class="wrap xl-gutter-40 inputs">
			<div class="col">


				<input class="large full" type="text" placeholder="Form Input 1"/>
				<input class="large full" type="text" placeholder="Form Input 2"/>
				<button class="large full"><a href="#">Form Group</a></button>


			</div>
			<div class="col">


				<input type="text" placeholder="Form Input 1"/>
				<input type="text" placeholder="Form Input 2"/>
				<button><a href="#">Form Group</a></button>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-40">
			<div class="col xl-center">

				<b class="left-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Left</b>

			</div>
			<div class="col xl-center">

				<b class="bottom-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Bottom</b>

			</div>
			<div class="col xl-center">

				<b class="top-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Top</b>

			</div>
			<div class="col xl-center">

				<b class="right-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Right</b>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Profile Picture With Image <br/><br/>
				<picture class="profile-picture larger" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture big" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture>


			</div>
			<div class="col xl-center">


				Profile Picture Without Image <br/><br/>
				<picture class="profile-picture larger" data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>


				<picture class="profile-picture big" data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture" data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Style Complete Pin <br/><br/>
				<pin class="small" data-pin-type="style" data-pin-complete="1">1</pin> <br/><br/>
				<pin class="mid" data-pin-type="style" data-pin-complete="1">1</pin> <br/><br/>
				<pin class="big" data-pin-type="style" data-pin-complete="1">1</pin> <br/><br/><br/>

				Style Pin <br/><br/>
				<pin class="big" data-pin-type="style">1</pin> <br/><br/>
				<pin class="mid" data-pin-type="style">1</pin> <br/><br/>
				<pin class="small" data-pin-type="style">1</pin>

			</div>
			<div class="col xl-center">

				Style Private Complete Pin <br/><br/>
				<pin class="small" data-pin-type="style" data-pin-private="1" data-pin-complete="1">2</pin> <br/><br/>
				<pin class="mid" data-pin-type="style" data-pin-private="1" data-pin-complete="1">2</pin> <br/><br/>
				<pin class="big" data-pin-type="style" data-pin-private="1" data-pin-complete="1">2</pin> <br/><br/><br/>

				Style Private Pin <br/><br/>
				<pin class="big" data-pin-type="style" data-pin-private="1">2</pin> <br/><br/>
				<pin class="mid" data-pin-type="style" data-pin-private="1">2</pin> <br/><br/>
				<pin class="small" data-pin-type="style" data-pin-private="1">2</pin>

			</div>
			<div class="col xl-center">

				Live Complete Pin <br/><br/>
				<pin class="small" data-pin-type="live" data-pin-complete="1">3</pin> <br/><br/>
				<pin class="mid" data-pin-type="live" data-pin-complete="1">3</pin> <br/><br/>
				<pin class="big" data-pin-type="live" data-pin-complete="1">3</pin> <br/><br/><br/>

				Live Pin <br/><br/>
				<pin class="big" data-pin-type="live">3</pin> <br/><br/>
				<pin class="mid" data-pin-type="live">3</pin> <br/><br/>
				<pin class="small" data-pin-type="live">3</pin>

			</div>
			<div class="col xl-center">

				Live Private Complete Pin <br/><br/>
				<pin class="small" data-pin-type="live" data-pin-private="1" data-pin-complete="1">4</pin> <br/><br/>
				<pin class="mid" data-pin-type="live" data-pin-private="1" data-pin-complete="1">4</pin> <br/><br/>
				<pin class="big" data-pin-type="live" data-pin-private="1" data-pin-complete="1">4</pin> <br/><br/><br/>

				Live Private Pin <br/><br/>
				<pin class="big" data-pin-type="live" data-pin-private="1">4</pin> <br/><br/>
				<pin class="mid" data-pin-type="live" data-pin-private="1">4</pin> <br/><br/>
				<pin class="small" data-pin-type="live" data-pin-private="1">4</pin>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Mouse Cursor <br/><br/>
				<pin class="mouse-cursor big active" data-pin-type="style" data-pin-private="0" style="position: static;">1</pin> <br/><br/>
				<pin class="mouse-cursor active" data-pin-type="style" data-pin-private="0" style="position: static;">1</pin>

			</div>
			<div class="col xl-center">

				Mouse Cursor Editable <br/><br/>
				<pin class="mouse-cursor big active" data-pin-type="live" data-pin-private="0" style="position: static;">2</pin> <br/><br/>
				<pin class="mouse-cursor active" data-pin-type="live" data-pin-private="0" style="position: static;">2</pin>

			</div>
			<div class="col xl-center">

				Mouse Cursor Existing <br/><br/>
				<pin class="mouse-cursor big active existing" data-pin-type="live" data-pin-private="0" style="position: static;">3</pin> <br/><br/>
				<pin class="mouse-cursor active existing" data-pin-type="live" data-pin-private="0" style="position: static;">3</pin>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<?php //require view("modules/components-pin-window") // !!! ?>


		<b>Popups</b><br><br>
		<div class="wrap xl-gutter-40">
			<div class="col">


				<a href="#" data-modal="add-new" data-type="project">Click here for a New Project Popup</a><br><br>

				<a href="#" data-modal="add-new" data-type="page">Click here for a New Page Popup</a><br><br>

				<a href="#" data-modal="share" data-type="project" data-id="1" data-object-name="Twelve12" data-iamowner="yes">Click here for Sharing a Project Popup</a><br><br>

				<a href="#" data-modal="share" data-type="page" data-id="1" data-object-name="Home" data-iamowner="yes">Click here for Sharing a Page Popup</a><br><br>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>

	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>