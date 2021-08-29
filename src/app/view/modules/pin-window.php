<div id="pin-window" class="loading"
	data-pin-id="0"
	data-pin-type="content"
	data-pin-private="0"
	data-pin-complete="0"
	data-pin-x="30"
	data-pin-y="30"
	data-pin-modification-type=""
	data-revisionary-content-edited="0"
	data-revisionary-style-changed="no"
	data-revisionary-showing-style-changes="no"
	data-has-comments="no"
	data-revisionary-showing-content-changes="0"
	data-revisionary-index="0"
>

	<div class="wrap xl-flexbox xl-between top-actions">
		<div class="col move-window left-tooltip" data-tooltip="Drag & Drop the pin window to detach from the pin.">
			<i class="fa fa-arrows-alt"></i>
		</div>
		<div class="col">

			<div class="wrap xl-flexbox xl-between actions">
				<div class="col action dropdown click-to-open">

					<pin
						class="chosen-pin"
						data-pin-type="content"
						data-pin-private="0"
					></pin>
					<a href="#"><span class="pin-label">Content Edit</span> <i class="fa fa-caret-down"></i></a>

					<ul class="xl-left type-convertor">

						<li class="convert-to-private">
							<a href="#" class="xl-flexbox xl-middle">
								<pin data-pin-private="1" data-pin-modification-type="null"></pin>
								<div>Make <span>Private Pin</span></div>
							</a>
						</li>

						<li class="convert-to-content">
							<a href="#" class="xl-flexbox xl-middle">
								<pin data-pin-type="content" data-pin-private="0" data-pin-modification-type=""></pin>
								<div>Make <b class="public">Public</b> <span>Content Pin</span></div>
							</a>
						</li>

						<li class="convert-to-style">
							<a href="#" class="xl-flexbox xl-middle">
								<pin data-pin-type="style" data-pin-private="0" data-pin-modification-type="null"></pin>
								<div>Make <b class="public">Public</b> <span>Style Pin</span></div>
							</a>
						</li>

						<li class="convert-to-comment">
							<a href="#" class="xl-flexbox xl-middle">
								<pin data-pin-type="comment" data-pin-private="0" data-pin-modification-type="null"></pin>
								<div>Make <b class="public">Public</b> <span>Comment Pin</span></div>
							</a>
						</li>

					</ul>

				</div>
				<div class="col action" data-tooltip="Coming soon." style="display: none !important;">

					<!-- <i class="fa fa-user-o"></i> -->
					<i class="fa fa-tag"></i>
					<span>TAGS</span>

				</div>
				<div class="col action">
					<a href="#" class="center-tooltip bottom-tooltip device-specific" data-tooltip="Only For Current Screen">
						<i class="fa <?=$screenIcon?>"></i>
					</a>
				</div>
			</div>

		</div>
		<div class="col">
			<a href="#" class="close-button" data-tooltip="Close this pin window when you're done here."><i class="fa fa-check"></i></a>
		</div>
	</div>

	<div class="image-editor">

		<div class="wrap xl-1">
			<div class="col section-title">

				<i class="fa fa-image"></i> IMAGE <i class="fa fa-circle edited-sign"></i>

			</div>
			<div class="col section-content" style="padding-top: 10px;">

				<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
					<div class="col title">Drag & Drop or <span class="select-file">Select File</span></div>
					<div class="col">

						<a href="#" class="switch edits-switch original">
							<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
							SHOW ORIGINAL
						</a>

					</div>
				</div>

				<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
					<div class="col">ORIGINAL IMAGE:</div>
					<div class="col edits-switch-wrap">

						<a href="#" class="switch edits-switch changes">
							<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
							SHOW CHANGED
						</a>

					</div>
				</div>

				<div class="wrap xl-1">
					<div class="col">
						<div class="edit-content changes uploader">

							<div class="bar"></div>
							<img class="new-image" src=""/>
							<div class="info"><span><span style="text-decoration: underline;">Click here</span> or drag here your image for preview</span></div>
							<form action="" id="pin-image-form" method="POST" enctype="multipart/form-data">
								<input type="file" name="image" id="filePhoto" class="pin-image" accept=".gif,.jpg,.jpeg,.png" data-max-size="15000000" />
							</form>

						</div>
						<div class="edit-content original">
							<img class="original-image" src=""/>
						</div>
					</div>
				</div>
				<div class="wrap xl-2 difference-switch-wrap">
					<a href="#" target="_blank" class="col xl-left switch image-url">
						<i class="fa fa-link"></i> OPEN IMAGE
					</a>
					<a href="#" class="col xl-right switch remove-image">
						<i class="fa fa-unlink"></i> REMOVE IMAGE
					</a>
				</div>

			</div>
		</div>

	</div>

	<div class="content-editor">

		<div class="wrap xl-1">
			<div class="col section-title">

				<i class="fa fa-pencil-alt"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

			</div>
			<div class="col section-content" style="padding-top: 10px;">

				<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
					<div class="col title">EDIT CONTENT:</div>
					<div class="col">

						<a href="#" class="switch edits-switch original">
							<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
							SHOW ORIGINAL
						</a>

					</div>
				</div>

				<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
					<div class="col">
						<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
						ORIGINAL CONTENT:
					</div>
					<div class="col edits-switch-wrap">

						<a href="#" class="switch edits-switch changes">
							<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
							SHOW CHANGED
						</a>

					</div>
				</div>

				<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap differences">
					<div class="col"><i class="fa fa-random"></i> DIFFERENCE:</div>
					<div class="col edits-switch-wrap">

						<a href="#" class="switch edits-switch changes xl-hidden">
							<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
							SHOW CHANGED
						</a>

					</div>
				</div>

				<div class="wrap xl-1 content-boxes">
					<div class="col">
						<div class="edit-content changes"></div>
						<div class="edit-content original"></div>
						<div class="edit-content differences"></div>
					</div>
				</div>

				<div class="wrap xl-2 difference-switch-wrap" style="padding-left: 10px;">
					<a href="#" class="col switch reset-content">
						<span><i class="fa fa-unlink"></i> RESET CHANGES</span>
					</a>
					<a href="#" class="col xl-right switch difference-switch">
						<i class="fa fa-random"></i> <span class="diff-text">SHOW DIFFERENCE</span>
					</a>
				</div>

			</div>
		</div>

	</div>

	<div class="visual-editor">

		<div class="wrap xl-1">
			<div class="col section-title collapsed">

				<i class="fa fa-sliders-h"></i> STYLE <i class="fa fa-circle edited-sign"></i>

			</div>
			<div class="col section-content options">

				<ul class="no-bullet options" style="margin-bottom: 0;">
					<li class="current-element">

						<span class="css-selector"><b>EDIT STYLE:</b> <span class="element-tag">tag</span><span class="element-id">#id</span><span class="element-class">.class</span></span>

						<a href="#" class="switch show-original-css" style="position: absolute; right: 0; top: 5px; z-index: 1;">
							<span class="original"><img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/> SHOW ORIGINAL</span>
							<span class="changes"><img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/> SHOW CHANGES</span>
						</a>

						<a href="#" class="switch reset-css" style="position: absolute; right: 0; top: 22px; z-index: 1;">
							<span><i class="fa fa-unlink"></i>RESET CHANGES</span>
						</a>

					</li>
					<li class="main-option choice">

						<a href="#" data-edit-css="display" data-value="block" data-default="none" class="active"><i class="fa fa-eye"></i> Show</a> |
						<a href="#" data-edit-css="display" data-value="none" data-default="block"><i class="fa fa-eye-slash"></i> Hide</a>

					</li>
					<li class="main-option dropdown click-to-open edit-opacity hide-when-hidden">

						<a href="#"><i class="fa fa-low-vision"></i> Opacity <i class="fa fa-angle-down"></i></a>
						<ul class="no-delay full">
							<li>

								<input type="range" min="0" max="1" step="0.01" value="1" class="range-slider" id="edit-opacity" data-edit-css="opacity" data-default="1"> <div class="percentage">100</div>

							</li>
						</ul>

					</li>
					<li class="main-option dropdown click-to-open hide-when-hidden">

						<a href="#"><i class="fa fa-font"></i> Text & Item <i class="fa fa-angle-down"></i></a>
						<ul class="no-delay">
							<li class="choice">

								<label class="main-option sub"><span class="inline"><i class="fa fa-font"></i> Size</span> <input type="text" class="increaseable" data-edit-css="font-size" data-default="initial"></label>
								<label class="main-option sub"><span class="inline"><i class="fa fa-text-height"></i> Line</span> <input type="text" class="increaseable" data-edit-css="line-height" data-default="normal"></label>

							</li>
							<li class="main-option sub choice">

								<span class="inline"><i class="fa fa-tint"></i> Color</span> <input type="color" data-edit-css="color" data-default="initial">

							</li>
							<li class="main-option sub choice selectable">

								<a href="#" data-edit-css="font-weight" data-value="bold" data-default="normal"><i class="fa fa-bold"></i> Bold</a> |
								<a href="#" data-edit-css="font-style" data-value="italic" data-default="normal"><i class="fa fa-italic"></i> Italic</a> |
								<a href="#" data-edit-css="text-decoration-line" data-value="underline" data-default="none"><i class="fa fa-underline"></i> Underline</a>

							</li>
							<li class="main-option sub choice">

								<a href="#" data-edit-css="text-align" data-value="left" data-default="right"><i class="fa fa-align-left"></i> Left</a> |
								<a href="#" data-edit-css="text-align" data-value="center" data-default="left"><i class="fa fa-align-center"></i> Center</a> |
								<a href="#" data-edit-css="text-align" data-value="justify" data-default="left"><i class="fa fa-align-justify"></i> Justify</a> |
								<a href="#" data-edit-css="text-align" data-value="right" data-default="left"><i class="fa fa-align-right"></i> Right</a>

							</li>
						</ul>
					</li>
					<li class="main-option dropdown click-to-open hide-when-hidden">
						<a href="#"><i class="fa fa-layer-group"></i> Background <i class="fa fa-angle-down"></i></a>
						<ul class="no-delay full">
							<li class="main-option sub choice">

								<span class="inline"><i class="fa fa-fill-drip"></i> Color:</span>
								<input type="color" data-edit-css="background-color" data-default="initial">

							</li>
							<li class="main-option sub choice">

								<span class="inline"><i class="fa fa-image"></i> Image URL:</span> <input type="url" data-edit-css="background-image" data-default="none" class="full no-padding" />

							</li>
							<li class="main-option sub choice hide-when-no-image">

								<span><i class="fa fa-crosshairs"></i> Position:</span>

								<span class="inline">X:</span> <input type="text" class="increaseable" data-edit-css="background-position-x" data-default="initial"/>
								<span class="inline">Y:</span> <input type="text" class="increaseable" data-edit-css="background-position-y" data-default="initial" />

							</li>
							<li class="main-option sub choice hide-when-no-image">

								<span><i class="fa fa-arrows-alt-v"></i> Size: </span>

								<a href="#" data-edit-css="background-size" data-value="auto" data-default="cover">Auto</a> |
								<a href="#" data-edit-css="background-size" data-value="cover" data-default="auto">Cover</a> |
								<a href="#" data-edit-css="background-size" data-value="contain" data-default="auto">Contain</a>

							</li>
							<li class="main-option sub choice hide-when-no-image">

								<span><i class="fa fa-redo"></i> Repeat: </span>

								<a href="#" data-edit-css="background-repeat" data-value="no-repeat" data-tooltip="No Repeat" data-default="repeat-x"><i class="fa fa-compress-arrows-alt"></i></a> |
								<a href="#" data-edit-css="background-repeat" data-value="repeat" data-tooltip="Repeat X and Y" data-default="no-repeat"><i class="fa fa-arrows-alt"></i></a> |
								<a href="#" data-edit-css="background-repeat" data-value="repeat-x" data-tooltip="Repeat X" data-default="no-repeat"><i class="fa fa-long-arrow-alt-right"></i></a> |
								<a href="#" data-edit-css="background-repeat" data-value="repeat-y" data-tooltip="Repeat Y" data-default="no-repeat"><i class="fa fa-long-arrow-alt-down"></i></a>

							</li>
						</ul>
					</li>
					<li class="main-option dropdown click-to-open hide-when-hidden" data-tooltip="Experimental">
						<a href="#"><i class="fa fa-object-group"></i> Spacing & Positions <i class="fa fa-angle-down"></i></a>
						<ul class="no-delay full" style="width: auto;">
							<li>

								<div class="css-box">

									<div class="layer positions">

										<div class="main-option sub input top"><input type="text" data-edit-css="top" data-default="initial"/></div>
										<div class="main-option sub input right"><input type="text" data-edit-css="right" data-default="initial"/></div>
										<div class="main-option sub input bottom"><input type="text" data-edit-css="bottom" data-default="initial"/></div>
										<div class="main-option sub input left"><input type="text" data-edit-css="left" data-default="initial"/></div>


										<div class="layer margins">

											<div class="main-option sub input top"><input type="text" data-edit-css="margin-top" data-default="initial"/></div>
											<div class="main-option sub input right"><input type="text" data-edit-css="margin-right" data-default="initial"/></div>
											<div class="main-option sub input bottom"><input type="text" data-edit-css="margin-bottom" data-default="initial"/></div>
											<div class="main-option sub input left"><input type="text" data-edit-css="margin-left" data-default="initial"/></div>


											<div class="layer borders">

												<div class="main-option sub input top"><input type="text" data-edit-css="border-top-width" data-default="initial"/></div>
												<div class="main-option sub input right"><input type="text" data-edit-css="border-right-width" data-default="initial"/></div>
												<div class="main-option sub input bottom"><input type="text" data-edit-css="border-bottom-width" data-default="initial"/></div>
												<div class="main-option sub input left"><input type="text" data-edit-css="border-left-width" data-default="initial"/></div>



												<div class="main-option sub input top left middle"><input type="text" data-edit-css="border-style" data-default="initial"></div>
												<div class="main-option sub input top right middle"><input type="color" data-edit-css="border-color" data-default="initial"></div>

												<div class="main-option sub input top left"><input type="text" data-edit-css="border-top-left-radius" data-default="initial"/><span>Radius</span></div>
												<div class="main-option sub input top right"><input type="text" data-edit-css="border-top-right-radius" data-default="initial"/><span>Radius</span></div>
												<div class="main-option sub input bottom left"><span>Radius</span><input type="text" data-edit-css="border-bottom-left-radius" data-default="initial"/></div>
												<div class="main-option sub input bottom right"><span>Radius</span><input type="text" data-edit-css="border-bottom-right-radius" data-default="initial"/></div>


												<div class="layer paddings">

													<div class="main-option sub input top"><input type="text" data-edit-css="padding-top" data-default="initial"/></div>
													<div class="main-option sub input right"><input type="text" data-edit-css="padding-right" data-default="initial"/></div>
													<div class="main-option sub input bottom"><input type="text" data-edit-css="padding-bottom" data-default="initial"/></div>
													<div class="main-option sub input left"><input type="text" data-edit-css="padding-left" data-default="initial"/></div>


													<div class="layer sizes">

														<span class="main-option sub width"><input type="text" data-edit-css="width" data-default="initial"/></span> x
														<span class="main-option sub height"><input type="text" data-edit-css="height" data-default="initial"/></span>

													</div>

												</div>

											</div>

										</div>

									</div>

								</div>

							</li>
						</ul>
					</li>
				</ul>

			</div>
		</div>

	</div>

	<div class="comments">

		<div class="wrap xl-1">
			<div class="col section-title xl-hiddenn">

				<i class="fa fa-comment-dots"></i> COMMENTS <i class="fa fa-circle edited-sign"></i>

			</div>
			<div class="col section-content">

				<div class="activities">
					<div class="wrap xl-flexbox xl-middle createdby">
						<div class="col xl-1-9 profile-image">
	
							<picture class="profile-picture">
								<span>BT</span>
							</picture>
	
						</div>
						<div class="col xl-8-9 activity-info">
							<b><span class="name">Bill TAS</span></b> created this pin.
							<span class="date">12 minutes ago</span>
						</div>
					</div>
	
					<div class="pin-comments">
	
					</div>

				</div>


				<div class="comment-actions">

					
					<div class="wrap xl-table xl-middle xl-gutter-8">
						<div class="col comment-input-col">
						
							<form action="" method="post" id="comment-sender">
								<textarea class="comment-input resizeable" rows="1" placeholder="Type your comments, and hit 'Enter'..." required></textarea>
							</form>

							<form action="" id="comment-attach-form" class="xl-hidden" method="POST" enctype="multipart/form-data">
								<input type="file" name="comment-attachment" id="comment-attachment" class="comment-attachment" accept=".gif,.jpg,.jpeg,.png,.svg,.pdf,.doc,.docx,.ppt,.pptx,.zip,.pages,.numbers" data-max-size="15000000" />
							</form>

							<div class="attachment-progress">
								<span class="percentage">%12</span>
								<span class="info">UPLOADING...</span>
							</div>

						</div>
						<div class="col xl-1-12 xl-right comment-icons-col">
							<a href="#" class="send-comment" data-tooltip="Send the comment"><i class="fa fa-paper-plane"></i></a>
							<label for="comment-attachment" class="attach" data-tooltip="Upload a file"><i class="fa fa-paperclip"></i></label>
							<a href="#" class="abort-attachment" data-tooltip="Cancel the upload"><i class="fa fa-times"></i></a>
						</div>
					</div>


				</div>

			</div>
		</div>



	</div>

	<div class="bottom-actions">

		<div class="wrap xl-flexbox xl-between">
			<div class="col action dropdown">
				<a href="#">
					<i class="fa fa-pencil-square-o"></i> MARK <i class="fa fa-caret-down"></i>
				</a>
				<ul>
					<li>
						<a href="#" class="xl-left draw-rectangle" data-tooltip="Coming soon." style="padding-right: 15px;">
							<img src="<?=asset_url('icons/mark-rectangle.png')?>" width="15" height="10" alt=""/>
							RECTANGLE
						</a>
					</li>
					<li>
						<a href="#" class="xl-left" data-tooltip="Coming soon.">
							<img src="<?=asset_url('icons/mark-ellipse.png')?>" width="15" height="14" alt=""/>
							ELLIPSE
						</a>
					</li>
				</ul>
			</div>
			<div class="col action">
				<a href="#" class="remove-pin"><i class="fa fa-trash-o"></i> REMOVE</a>
			</div>
			<div class="col action pin-complete">
				<a href="#" class="complete-pin" data-tooltip="Mark as resolved">
					<pin
						data-pin-type="comment"
						data-pin-private="0"
						data-pin-complete="1"
					></pin>
					DONE
				</a>
				<a href="#" class="incomplete-pin" data-tooltip="Mark as unresolved">
					<pin
						data-pin-type="comment"
						data-pin-private="0"
						data-pin-complete="0"
					></pin>
					INCOMPLETE
				</a>
			</div>
		</div>

	</div>

</div>