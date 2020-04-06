// When document is ready
$(function() {

	// Prevent clicking '#' links
	$('a[href="#"]').click(function(e) {
		e.preventDefault();
	});


	// Navigation out of the page
	var linkClickedOut = false;
	$(document).on('click', 'a[href]', function(e) {

		linkClickedOut = true;


		var link = $(this).attr('href');
		var linkAbsolute = $(this).prop('href');
		var target = $(this).prop('target') == "_blank" ? "_blank" : "_self";


		// Remove current page if no pins added
		if (
			currentPinType == "browse" &&
			!link.startsWith('#') &&
			!link.startsWith('javascript:') && // jshint ignore:line
			target == "_self" &&
			queryParameter(currentUrl(), 'new') == "page" &&
			queryParameter(linkAbsolute, 'new_screen') == null &&
			Pins.length == 0
		) {


			// Remove the page and then go to the link
			doAction('remove', 'page', page_ID, 'redirect', link);


			e.preventDefault();
			return false;
		}


	});


	// Detect cursor moves
	$(window).mousemove(function(e) {

		// Iframe offset
		offset = $('#the-page').offset();

		containerX = e.clientX - offset.left;
		containerY = e.clientY - offset.top;


		//console.log(hoveringPin);

	});


	// Tab opener
	$('.opener').click(function(e) {
		toggleTab( $(this) );

		e.preventDefault();
		return false;
	});



	// PINS LIST:

	// Comment Opener
	$(document).on('click', '.pins-list .pin-title', function(e) {

		$(this).toggleClass('close');

		var thePin = $(this).prev().children('pin');
		var pin_ID = thePin.attr('data-pin-id');
		var pinComments = $(this).next('.pin-comments[data-pin-id="'+ pin_ID +'"]');


		if (!$(this).hasClass('close')) {

			console.log('Getting comments for: ', pin_ID);

			getComments(pin_ID, pinComments);

		}



		e.preventDefault();
		return false;
	});


	// Hovering a pin from the pins list tab
	$(document).on('mouseover', '.pins-list > .pin', function(e) {


		var pin_ID = $(this).find('pin').attr('data-pin-id');
		var pin_type = $(this).find('pin').attr('data-pin-type');
		var pin_private = $(this).find('pin').attr('data-pin-private');
		var element_index = $(this).find('pin').attr('data-revisionary-index');


		// Outline
		$('#pins > pin:not([data-pin-id="'+ pin_ID +'"])').css('opacity', '0.2');
		outline(iframeElement(element_index), pin_private, pin_type);


		// Scroll
		scrollToPin(pin_ID);


		e.preventDefault();

	}).on('mouseout', '.pins-list > .pin', function(e) {


		iframeElement('html, body').stop();
		if (pinAnimationTimeout) clearTimeout(pinAnimationTimeout);

		$('#pins > pin').css('opacity', '');
		removeOutline();


		e.preventDefault();

	});


	// Filtering the pins
	$('.pins-filter > a').click(function(e) {


		console.log('SHOW THE PINS: ', filter);


		var filter = $(this).attr('data-filter');


		$('.pins-filter > a').removeClass('selected');
		$(this).addClass('selected');


		$('#pins, .pins-list').attr('data-filter', filter);


		relocatePins();


		if (history.pushState) {
		    var newurl = queryParameter(currentUrl(), 'filter', (filter == "all" ? '' : filter));
		    window.history.pushState({path:newurl},'',newurl);
		}


		e.preventDefault();
	});



	// PIN MODES:

	// Pin mode change
	$(document).on('click', '[data-switch-pin-type]', function(e) {

		var selectedPinType = $(this).attr('data-switch-pin-type');
		var selectedPinPrivate = $(this).attr('data-switch-pin-private');


		// Change the pin type
		switchPinType(selectedPinType, selectedPinPrivate);


		// Close modal
		if ( $(this).parent().hasClass('recommendation') ) closeModal();


		e.preventDefault();
		return false;
	});


	// Browse Switch
	$('.pin-mode a.browse-switcher').click(function(e) {


		// Close current pin window
		closePinWindow();


		if (currentPinType != "browse") {

			switchPinType('browse', 0);

		} else {

			currentPinTypeWas = currentPinTypeWas != "browse" ? currentPinTypeWas : "live";
			currentPinPrivateWas = currentPinPrivateWas != null ? currentPinPrivateWas : "0";

			switchPinType(currentPinTypeWas, currentPinPrivateWas);

		}


		e.preventDefault();
		return false;
	});


	$('.pin-type-selector').hover(function() {
		$('ul.pin-types').css('display', '');
	});



	// Rotateable devices
	$('.rotate').click(function(e) {


		if ( $(this).hasClass('rotated') ) {

			page_width = maxWidth = iframeWidth = realPageWidth;
			page_height = maxHeight = iframeHeight = realPageHeight;

			$(this).removeClass('rotated').find('span').text('Landscape');

		} else {

			page_width = maxWidth = iframeWidth = realPageHeight;
			page_height = maxHeight = iframeHeight = realPageWidth;

			$(this).addClass('rotated').find('span').text('Portrait');

		}


		// Reset the scale
		iframeScale = 1;


		// Update the iframe
		$('.iframe-container').css({ "width": page_width, "height": page_height });
		$('#the-page').attr({ "width": page_width, "height": page_height }).css({ "min-width": page_width, "min-height": page_height });


		// Scale the window
		$(window).resize();


		console.log('ROTATE IT', page_width, page_height);

	});



	// Iframe Fit to the screen
	$(window).resize(function(e) {


	    var page = $('#page');
	    var width = page.width();
	    var height = page.height() - 2;


	    // UPDATE THE CURRENT WINDOW SIZE FOR CUSTOM SCREEN ADDING
		// Show new values
		$('.screen-width').text(width);
		$('.screen-height').text(height);

		// Edit the input values
		$('input[name="page_width"]').attr('value', width);
		$('input[name="page_height"]').attr('value', height);

		// Update the URLs
		$('.add-phase, .new-screen[data-screen-id="11"]').each(function() {

			var currentURL = $(this).attr('href');

			var widthOnURL = getParameterByName('page_width', currentURL);
			var heightOnURL = getParameterByName('page_height', currentURL);

			var newURL = currentURL.replace('page_width='+widthOnURL, 'page_width='+width);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+height);

			$(this).attr('href', newURL);
			//console.log(newURL);

		});


		// IFRAME FIT TO THE SCREEN
	    width = width - 4; // -4 for the borders
		height = height - 2; // -2 for the borders
		

		// if (page_type == "image") {


		// 	// Early exit if smaller than the screen
		// 	if(width >= maxWidth && height >= maxHeight) {
		// 		$('#the-page').css({'transform': ''});
		// 		//$('.iframe-container').css({ width: '', height: '' });
		// 		return;
		// 	}


		// 	iframeScale = width / maxWidth;
		// 	if (iframeScale > 1) iframeScale = 1;

		// 	iframeWidth = maxWidth * iframeScale;
		// 	iframeHeight = maxHeight * iframeScale;


		// 	$('#the-page').css({
		// 		height: height / iframeScale,
		// 		'min-height': height / iframeScale
		// 	});


		// } else {


			// Early exit if smaller than the screen
			if(width >= maxWidth && height >= maxHeight) {
				$('#the-page').css({'transform': ''});
				//$('.iframe-container').css({ width: '', height: '' });
				return;
			}

			iframeScale = Math.min(width/maxWidth, height/maxHeight);
			iframeWidth = maxWidth * iframeScale;
			iframeHeight = maxHeight * iframeScale;


		//}


		// Update the iframe
		$('.iframe-container').css({ width: iframeWidth, height: iframeHeight });
		$('#the-page').css({'transform': 'scale(' + iframeScale + ')'});


		// Update the scale on info section
		$('.iframe-scale').text( iframeScale.toFixed(1) );


	}).resize();



	// PIN WINDOW:

	// Stop scrolling on content editor
	var scrollOnContent = false;
	$(document).on('mousewheel', '#pin-window .edit-content, #pin-window .pin-comments, #pin-window .comment-input', function(e) {

		scrollOnContent = true;
		//console.log('SCROLL ON CONTENT');

	});



	// Continue scrolling on any pin or the pin window
	$(document).on('mousewheel', '#pins > pin, #pin-window', function(e) {

		//if (!scrollOnContent) console.log('SCROLL ON WINDOW');

		var scrollDelta = e.originalEvent.wheelDelta;
		if (!scrollOnContent) iframe.scrollTop( scrollOffset_top - scrollDelta );

		scrollOnContent = false;

	});


	// Pin window draggable
	$("#pin-window").draggable({
		handle: ".move-window",
		cursor: "move",
		opacity: 0.7,
		containment: "#page",
		iframeFix: true,
		start: function( event, ui ) {

			// console.log('PIN WINDOW IS MOVING');
			$("#pin-window").addClass('moved');

		}
	});


	// Close pin window
	$('#pin-window .close-button').click(function(e) {

		var commentWritten = pinWindow().find('.comment-input').val();

		// Send the comment if unsend
		if( commentWritten ) $('#comment-sender').submit();

		// Close the window
		if (pinWindowOpen) closePinWindow(!commentWritten);

		e.preventDefault();

	});


	// Remove Pin
	$('#pin-window .remove-pin').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');

		// Delete it from DB
		if (
			pinWindow(pin_ID).attr('data-revisionary-content-edited') == "0" &&
			pinWindow(pin_ID).attr('data-revisionary-style-changed') == "no" &&
			pinWindow(pin_ID).attr('data-has-comments') == "no" &&
			pinWindow(pin_ID).attr('temporary') != ""
		) removePin(pin_ID);

		else if ( confirm('Are you sure you want to delete this pin and its modifications and comments?') )
			removePin(pin_ID);


		e.preventDefault();

	});


	// Device only Pin
	$(document).on('click', '#pin-window .device-specific:not(.active):not(.loading)', function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');


		makeDeviceSpecific(pin_ID, device_ID);


		e.preventDefault();

	});


	// Disable Device only Pin
	$(document).on('click', '#pin-window .device-specific.active:not(.loading)', function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');


		makeForAllDevices(pin_ID);


		e.preventDefault();

	});


	// Complete Pin
	$('#pin-window .pin-complete > a').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		var isComplete = pinWindow(pin_ID).attr('data-pin-complete') == "1";


		completePin(pin_ID, !isComplete);
		if (!isComplete) closePinWindow();


		e.preventDefault();

	});


	// Pin window section toggle
	$('#pin-window .section-title').click(function(e) {

		
		if ( $(this).hasClass('collapsed') ) { // Open

			// Close all the sections first
			$('#pin-window .section-title').not( $(this) ).addClass('collapsed');

			// Open this one
			$(this).removeClass('collapsed');

		} else { // Close
			
			// Only close this one
			$(this).addClass('collapsed');
		
		}


		// Update the location and size values first
		relocatePinWindow();


		e.preventDefault();

	});


	// Comment Sender
	$('#comment-sender').submit(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		var message = $(this).find('.comment-input').val();

		// Add it from DB
		sendComment(pin_ID, message);

		e.preventDefault();
		return false;

	});


	// Send button
	$('.send-comment').click(function(e) {

		// Submit the form
		$('#comment-sender').submit();

		e.preventDefault();
		return false;

	});


	// Pressing enter / enter + shift on comment input
	$('.comment-input').keypress(function(e) {

		if (e.keyCode == 13 && !e.shiftKey) {

			$('#comment-sender').submit(); // Submit your form here

			e.preventDefault();
			return false;
		}

	}).on('input', function() {

		pinWindow().attr('data-comment-written', ( $(this).val().length ? "yes" : "no" ));

	});


	// Comment attachment
	$(document).on('change', '#comment-attach-form input[name="comment-attachment"]', function() {


		var form = $('#comment-attach-form');
		var maxSize = $(this).attr('data-max-size');


	    var reader = new FileReader();
	    reader.onload = function(event) {


			// Temp data URL
			//var imageSrc = event.target.result;


			// Apply the change
			//form.find('.selected-image img').attr('src', imageSrc);


			form.submit();

	    };


		// If a file selected
        if ( $(this).get(0).files.length ) {


            var fileSize = $(this).get(0).files[0].size; // in bytes
            if (fileSize > maxSize) {

                alert('File size is more than ' + formatBytes(maxSize));
                return false;

            } else {

                console.log('File size is correct - ' + formatBytes(fileSize) + ', no more than ' + formatBytes(maxSize));
	        	reader.readAsDataURL( $(this).get(0).files[0] );

            }


		// If no file selected
        } else {

		    console.log('NO FILE');
			return false;

        }


	});


	// Comment attachment submission
	$(document).on('submit', '#comment-attach-form', function(e) {


		// console.log('UPLOAD THAT ATTACHMENT');
		// return false;
		// e.preventDefault();


		var pin_ID = parseInt( pinWindow().attr('data-pin-id') );
		var form = $(this);


		// Uploading state
		pinWindow().find('.comment-actions').addClass('uploading');


		// Start the process
		var uploadAttachmentProcessID = newProcess(true, "uploadAttachmentProcess");


		$.ajax({
			url: ajax_url+'?type=comment-attachment&pin_ID=' + pin_ID,
			type: 'POST',
			data:  new FormData(this),
			mimeType: "multipart/form-data",
			contentType: false,
			cache: false,
			processData: false,
			dataType: 'json',
			xhr: function() {


				var jqXHR = null;
				if ( window.ActiveXObject ) {

					jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );

				} else {

					jqXHR = new window.XMLHttpRequest();

				}


				// Upload progress
				jqXHR.upload.addEventListener( "progress", function ( evt ) {

					if ( evt.lengthComputable ) {

						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						console.log( 'Uploaded percent', percentComplete );


						editProcess(uploadAttachmentProcessID, percentComplete);
						pinWindow().find('.attachment-progress > .percentage').text(percentComplete + '%');
						pinWindow().find('.attachment-progress > .info').text('UPLOADING...');

						if (percentComplete == 100) pinWindow().find('.attachment-progress > .info').text('PROCESSING...');

					}

				}, false );


				// Download progress
				jqXHR.addEventListener( "progress", function ( evt ) {

					if ( evt.lengthComputable ) {

						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						console.log( 'Downloaded percent', percentComplete );

					}

				}, false );


				return jqXHR;
			},
			success: function(data, textStatus, jqXHR) {
				
				var imageUrl = data.new_url;
				var status = data.status;

				if (status != "success") {

					console.error('ERROR: ', status, data, imageUrl, textStatus, jqXHR);
					return false;

				}


				console.log('SUCCESS!', imageUrl, data, textStatus, jqXHR);


				// Finish the process
				endProcess(uploadAttachmentProcessID);


				// Refresh the comments list
				getComments(pin_ID);


				// Reset the form
				form.trigger("reset");


				// Uploading state
				pinWindow().find('.comment-actions').removeClass('uploading');


			},
			error: function(jqXHR, textStatus, errorThrown) {

				console.log('FAILED!!', errorThrown);

				
				// Finish the process !!!
				endProcess(uploadAttachmentProcessID);

			}
		});
		

		e.preventDefault();
		return false;

	});


	// Delete Comment
	$(document).on('click', '.delete-comment', function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		var comment_ID = $(this).attr('data-comment-id');

		// Delete it from DB if confirmed
		if ( confirm('Are you sure you want to delete this comment?') )
			deleteComment(pin_ID, comment_ID);

		e.preventDefault();
	});


	// Toggle original content
	$('.edits-switch').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		//var isBg = pinWindow().attr('data-has-bg') != "no";


		// Toggle the changes
		// if (isBg) toggleCSS(pin_ID);
		// else toggleChange(pin_ID);
		toggleChange(pin_ID);


		e.preventDefault();

	});


	// Toggle differences content
	$('.difference-switch').click(function(e) {

		pinWindow().toggleClass('show-differences');

		var diffText = pinWindow().hasClass('show-differences') ? "SHOW CHANGES" : "SHOW DIFFERENCE";
		var diffIcon = pinWindow().hasClass('show-differences') ? "fa-pencil-alt" : "fa-random";

		pinWindow().find('.content-editor span.diff-text').text(diffText);
		pinWindow().find('.content-editor .difference-switch > i').removeClass('fa-random', 'fa-pencil-alt').addClass(diffIcon);


		if (pinWindow().hasClass('show-differences')) {

			var originalContent = pinWindow().find('.content-editor .edit-content.original').html();
			var changedContent = pinWindow().find('.content-editor .edit-content.changes').html();


			// Difference check
			var diffContent = diffCheck(originalContent, changedContent);


			// Free users
			if (user_level_ID == 2) {
			
				diffContent = "<div class='xl-center'>Please upgrade to see the content differences.</div>";
				diffContent += "<div class='xl-center'><a href='/upgrade' class='upgrade-button' data-modal='upgrade'>UPGRADE NOW</a></div>";

			}


			// Add the differences content
			pinWindow().find('.content-editor .edit-content.differences').html( diffContent );

		}


		e.preventDefault();

	});


	$(document).on('mousedown', '.content-editor .edit-content.changes', function(e) {

		selectionFromContentEditor = true;

	}).on('mouseup', 'body', function(e) {

		selectionFromContentEditor = false;

	});


	// Image Uploader
	$('.pin-image').change(function() {

		var formElement = $(this).parent('form');
		var maxSize = $(this).attr('data-max-size');

		var pin_ID = pinWindow().attr('data-pin-id');
		var element_index = pinWindow(pin_ID).attr('data-revisionary-index');
		var changedElement = iframeElement(element_index);


	    var reader = new FileReader();
	    reader.onload = function(event) {


			var imageSrc = event.target.result;


			//console.log('REGISTERED CHANGES', changes);


			// Apply the temporary change
			pinWindow(pin_ID).find('.image-editor .uploader img').attr('src', imageSrc);


			// Submit the form
			formElement.submit();


	    };


		// If a file selected
        if ( $(this).get(0).files.length ) {


            var fileSize = $(this).get(0).files[0].size; // in bytes
            if (fileSize > maxSize) {

                alert('File size is more than ' + formatBytes(maxSize));
                return false;

            } else {

                console.log('File size is correct - '+formatBytes(fileSize)+', no more than '+formatBytes(maxSize));
				reader.readAsDataURL($(this).get(0).files[0]);
				
				pinWindow(pin_ID).find('.uploader').addClass('uploading');

            }


		// If no file selected
        } else {

		    console.log('NO FILE');
            return false;
        }


		console.log('CHANGED');

	});


	// Image uploader AJAX
	$('#pin-image-form').submit(function(e) {


		var pin_ID = pinWindow().attr('data-pin-id');
		var pin = getPin(pin_ID);
		var pinIndex = Pins.indexOf(pin);
		var element_index = pinWindow(pin_ID).attr('data-revisionary-index');
		var changedElement = iframeElement(element_index);
		var form = $(this);


		// Start the process
		var uploadImageProcessID = newProcess(true, "uploadImageProcess");


		$.ajax({
			url: ajax_url+'?type=pin-photo-upload&pin_ID=' + pin_ID,
			type: 'POST',
			data:  new FormData(this),
			mimeType: "multipart/form-data",
			contentType: false,
			cache: false,
			processData: false,
			dataType: 'json',
			xhr: function() {


				var jqXHR = null;
				if ( window.ActiveXObject ) {

					jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );

				} else {

					jqXHR = new window.XMLHttpRequest();

				}


				// Upload progress
				jqXHR.upload.addEventListener( "progress", function ( evt ) {

					if ( evt.lengthComputable ) {

						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						console.log( 'Uploaded percent', percentComplete );

						pinWindow(pin_ID).find('.uploader .bar').css('width', percentComplete + '%').text(percentComplete +'%');

						if (percentComplete == 100) pinWindow(pin_ID).find('.uploader .bar').text('Processing...');

					}

				}, false );


				// Download progress
				jqXHR.addEventListener( "progress", function ( evt ) {

					if ( evt.lengthComputable ) {

						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						console.log( 'Downloaded percent', percentComplete );

					}

				}, false );


				return jqXHR;
			},
			success: function(data, textStatus, jqXHR) {
				
				var imageUrl = data.new_url;
				var status = data.status;

				if (status != "success") {

					console.error('ERROR: ', status, data, imageUrl, textStatus, jqXHR);
					return false;

				}


				console.log('SUCCESS!', imageUrl, data, textStatus, jqXHR);

				// Update the global
				Pins[pinIndex].pin_modification = imageUrl;


				// Reset the form
				form.trigger('reset');


				// Update the images
				pinWindow(pin_ID).find('.image-editor .uploader img').attr('src', imageUrl);
				pinWindow(pin_ID).find('.image-editor a.image-url').attr('href', imageUrl);


				// Update the element, pin and pin window status
				if ( changedElement.prop('tagName').toUpperCase() == "IMAGE" )
					changedElement.attr('xlink:href', imageUrl);
				else
					changedElement.attr('src', imageUrl).removeAttr('srcset');


				updateAttributes(pin_ID, 'data-revisionary-content-edited', "1");
				updateAttributes(pin_ID, 'data-revisionary-showing-content-changes', "1");


				pinWindow(pin_ID).find('.uploader').removeClass('uploading');
				pinWindow(pin_ID).find('.uploader .bar').text('');


				console.log('Image changed.');


				// Finish the process
				endProcess(uploadImageProcessID);


			},
			error: function(jqXHR, textStatus, errorThrown) {

				console.log('FAILED!!', errorThrown);

				pinWindow(pin_ID).find('.uploader').removeClass('uploading');
				pinWindow(pin_ID).find('.uploader .bar').text('ERROR');

			}
		});
		

		e.preventDefault();

	});


	// Select File
	$('.select-file').click(function(e) {

		$('.pin-image').click();

		e.preventDefault();

	});


	// Open Image !!! Add Lightbox
	$('.image-url').click(function(e) {

		window.open( $(this).attr('href') , '_blank' );
		e.preventDefault();

	});


	// Remove Image
	$('.remove-image').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		//var isBg = pinWindow().attr('data-has-bg') != "no";


		// // Remove the image on this element
		// if (isBg) resetCSS(pin_ID);
		// else removeImage(pin_ID);
		removeImage(pin_ID);


		e.preventDefault();

	});


	// CSS EDITS
	var doChangeCSS = {};
	$('[data-edit-css]').on('click input', function(e) {

		//console.log('TAG NAME: ', $(this).prop('tagName').toUpperCase().toUpperCase(), e.type );

		// Prevent saving when clicking any input
		if ( $(this).prop('tagName').toUpperCase().toUpperCase() == "INPUT" && e.type == "click" ) return true;


		// Mark as changed
		$(this).attr('data-revisionary-style-changed', 'yes');
		$(this).parents('.main-option').addClass('changed');


		var property = $(this).attr('data-edit-css');
		var isActive = $(this).hasClass('active');

		var defaultValue = $(this).attr('data-default');
		var value = $(this).attr('data-value') || $(this).val() || defaultValue;


		value = isActive ? defaultValue : value;


		var pin_ID = pinWindow().attr('data-pin-id');
		var element_index = pinWindow(pin_ID).attr('data-revisionary-index');
		var options = pinWindow(pin_ID).find('ul.options');


		console.log('EDIT CSS: ', property, value, element_index);


		// Stop the auto-refresh
		stopAutoRefresh();


		// Disable the active status
		options.find('a[data-edit-css="'+ property +'"]').removeClass('active');
		options.find('a[data-edit-css="'+ property +'"][data-value="'+ value +'"]').addClass('active');


		// Add the value
		options.attr('data-' + property, value);


		// Prepare the CSS data
		var css = {};
		var properties = options.find('[data-edit-css][data-revisionary-style-changed="yes"]');
		$(properties).each(function(i, propertyElement) { //console.log('PROPERTIES: ', propertyElement);

			var propertyName = $(propertyElement).attr('data-edit-css');
			var propertyDefaultValue = $(propertyElement).attr('data-default');
			var propertyValue = $(propertyElement).attr('data-value') || $(propertyElement).val() || defaultValue;

			css[propertyName] = options.attr('data-'+propertyName);

		});


		// Prepare the CSS declarations
		var cssCode = "";
		$.each( css, function( key, value ) {


			// Skip if display is block
			if (key == "display" && value != "none") return true;


			// Background Image URL
			if (key == "background-image" && value.includes("//")) value = "url("+ value +")";


			cssCode = cssCode + key + ":" + value + " !important; ";


		});


		// Instant update the CSS
		updateCSS(pin_ID, cssCode);


		// Remove unsent job
		if (doChangeCSS[element_index]) clearTimeout(doChangeCSS[element_index]);

		// Send changes to DB after 1 second
		doChangeCSS[element_index] = setTimeout(function(){

			saveCSS(pin_ID, css);

		}, 1000);


		relocatePins();
		e.preventDefault();

	});


	// Reset CSS
	$('.reset-css').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		var element_index = parseInt(pinWindow(pin_ID).attr('data-revisionary-index'));


		if ( confirm('Are you sure you want to reset all your view options?') ) {


			// Reset CSS on DB
			resetCSS(pin_ID);


			// Remove the styles
			iframeElement('style[data-pin-id="'+ pin_ID +'"]').remove();


		}


		e.preventDefault();

	});


	// Reset Content
	$('.reset-content').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		var element_index = parseInt(pinWindow(pin_ID).attr('data-revisionary-index'));


		if ( confirm('Are you sure you want to revert your content changes?') ) {


			// Reset content on DB
			resetContent(pin_ID);


		}


		e.preventDefault();

	});


	// Show original CSS toggle
	$('.show-original-css').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');


		toggleCSS(pin_ID);


		e.preventDefault();

	});


	// Convert Pin
	$('.type-convertor > li > a').click(function(e) {

		var pin_ID = pinWindow().attr('data-pin-id');
		var element_index = parseInt(pinWindow(pin_ID).attr('data-revisionary-index'));
		var targetPin = $(this).children('pin');


		// Confirm if converting to style pin
		if (
			pinWindow(pin_ID).attr('data-pin-type') == "live" &&
			targetPin.attr('data-pin-type') == "style" &&
			!confirm('Are you sure you want to convert this live pin to a style pin? All your changes will be reverted.')
		) return false;


		// Confirm if converting to comment pin
		if (
			pinWindow(pin_ID).attr('data-pin-type') == "live" &&
			targetPin.attr('data-pin-type') == "comment" &&
			!confirm('Are you sure you want to convert this live pin to a comment pin? All your changes will be reverted.')
		) return false;

		if (
			pinWindow(pin_ID).attr('data-pin-type') == "style" &&
			targetPin.attr('data-pin-type') == "comment" &&
			!confirm('Are you sure you want to convert this style pin to a comment pin? All your changes will be reverted.')
		) return false;


		// Remove the image on this element
		convertPin(pin_ID, targetPin);


		e.preventDefault();

	});


	// SURVEY POPUPS
	$('.ask-showing-correctly [data-popup="close"]').click(function(e) {

		$('.ask-showing-correctly').removeClass('open');

		e.preventDefault();

	});


	// PIN HOVERING
	var pinClicked = false;
	$(document).on('mouseover', '#pins > pin, #pin-window', function(e) {

		//console.log( 'Hovering a Pin: ' + $(this).attr("data-pin-type"), $(this).attr("data-pin-private"), $(this).attr("data-pin-complete"), $(this).attr("data-revisionary-index") );


		hoveringPin = parseInt( $(this).attr("data-pin-id") );


		// Reset the pin opacity
		if (!pinWindowOpen) $('#pins > pin').css('opacity', '');


		// Hide the cursor
		cursor.removeClass('active');


		// Clear all outlines
		removeOutline();


		// Outline the element
		var hoveringPinType = $(this).attr("data-pin-type");
		var hoveringPinPrivate = $(this).attr("data-pin-private");
		var hoveringPinIndex = $(this).attr("data-revisionary-index");
		if (hoveringPinType == "live" || hoveringPinType == "style" ) outline(iframeElement(hoveringPinIndex), hoveringPinPrivate, hoveringPinType);


		e.preventDefault();

	}).on('mousedown', '#pins > pin', function(e) {

		//console.log('CLICKED TO A PIN!');


		focusedPin = $(this);
		hoveringPin = parseInt(focusedPin.attr('data-pin-id'));
		pinClicked = true;
		pinDragging = false;


		// Disable the iframe
		$('#the-page').css('pointer-events', 'none');


		e.preventDefault();

	}).on('mousemove', function(e) {

		// Nothing to do yet

	}).on('mouseup', function(e) {


		if (pinClicked) {

			//console.log('PIN UN-CLICKED!');


			pinClicked = false;
			hoveringPin = false;


			// Enable the iframe
			$('#the-page').css('pointer-events', 'auto');


			// Toggle the pin window even if just a little dragging
		    if (!pinDragging)
				togglePinWindow( parseInt(focusedPin.attr('data-pin-id')) );


			focusedPin = null;

			e.preventDefault();
		}

	}).on('mousewheel', '#pins > pin', function(e) {
		
		
		//console.log('SCROLLING ON PIN!');
		scrollOnPin = true;


	}).on('mouseout', '#pins > pin, #pin-window', function(e) {

		//console.log('MOUSE OUT FROM PIN!', pinDragging);
		hoveringPin = false;
		scrollOnPin = false;


		var pin_ID = parseInt($(this).attr('data-pin-id'));
		if (!pinWindowOpen) relocatePin(pin_ID);


		// Clear all outlines
		removeOutline();


		// Show the cursor
		if (cursorActive && !pinDragging) cursor.addClass('active');


		e.preventDefault();

	});


	// Before closing the window
	$(window).on('beforeunload', function(e) {


		// Remove current page if no pins added
		if ( !linkClickedOut && currentPinType == "browse" && queryParameter(currentUrl(), 'new') == "page" && Pins.length == 0 ) {


			// Remove the page and then go to the link
			doAction('remove', 'page', page_ID, "autoremove");


		}


	});


	// Resizable textarea
	autosize($('textarea.resizeable'));


	// Pin links
	$(document).on('click', '[data-go-pin]', function(e) {

		var pin_ID = parseInt( $(this).attr('data-go-pin') );

		// Pin might be in another page
		if ( !getPin(pin_ID) ) {

			var url = $(this).attr('href');
			window.location.href = url;

			return false;
		}


		scrollToPin(pin_ID, true, true);
		e.preventDefault();

	});


	// Keyboard bindings
    $(document).keydown(function(e) {


		// Shift key toggle browse mode
		if (e.shiftKey) shifted = true;
		if (shifted && !pinWindowOpen && currentPinType != "browse") {

			shiftToggle = true;
			console.log('SHIFTED');

			currentPinTypeWas = currentPinType;
			currentPinPrivateWas = currentPinPrivate;
			toggleCursorActive(true); // Force close
			currentPinType = "browse";
			currentPinPrivate = 0;

		}


		// Escape
	    if(e.keyCode == 27 && pinWindowOpen) { 

			console.log('CLOSE PIN WINDOW via ESC');
			closePinWindow();

		    e.preventDefault();
		    return false;
	    }


    });

    $(document).keyup(function(e) {


		if (shifted && shiftToggle && !pinWindowOpen && currentPinType == "browse") {

			shiftToggle = false;
			console.log('UNSHIFTED');

			currentPinType = currentPinTypeWas;
			currentPinPrivate = currentPinPrivateWas;
			toggleCursorActive(false, true); // Force Open

		}


		shifted = false;

    });


/*
	// Detect Window switches for the correct focusing
	$(window).on('blur', function(e) {


		console.log('BLURRED');


	}).on('focus', function(e) {


		console.log('FOCUSSED');


	});
*/


/*
	// HASH CHANGE
	$(window).bind('hashchange', function(e) {

		var goToPin_ID = parseInt( window.location.hash.replace('#', '') );

		if ( !pinWindowOpen || pinWindow().attr('data-pin-id') != goToPin_ID ) {

			var goToPin_ID = parseInt( window.location.hash.replace('#', '') );
			console.log('Going to the Pin #', goToPin_ID);

			scrollToPin(goToPin_ID, true);

		}

	});
*/


});


// When everything is loaded
$(window).on("load", function (e) {


	// COLOR PICKER PLUGIN
	$("input[type='color']").spectrum({
		preferredFormat: "hex",
	    showInitial: true,
	    showInput: true,
	    showAlpha: true,
	    allowEmpty: true,

	    showPalette: true,
	    showPaletteOnly: true,
	    togglePaletteOnly: true,
	    togglePaletteMoreText: 'more',
	    togglePaletteLessText: 'less',

		chooseText: "Close",
	    move: function(color) {

		    $(this).val(color.toRgbString()).attr('data-value', color.toRgbString()).trigger('input');

	    },
	    change: function(color) {

		    $(this).trigger('input');

		}
	}).on("dragstart.spectrum", function(e, color) {

		console.log('DRAG STARTED', color.toHexString());
		$(this).val(color.toHexString()).spectrum("set", color.toHexString());

	});


});