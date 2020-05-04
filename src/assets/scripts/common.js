/*jshint multistr: true */

// Notification Auto-Refresh
var notificationAutoRefreshTimer;
var notificationAutoRefreshInterval = 10000;
var notificationAutoRefreshRequest = null;


$(function() {


	// Prevent clicking '#' links
	$(document).on('click', 'a[href="#"], .name-field.editing, .no-link', function(e) {
		e.preventDefault();
	});


	// Click to open dropdowns
	$(document).on('click', '.click-to-open > a:not(.editing)', function(e) {

		$(this).toggleClass('open');
		$(this).find('.fa-angle-down').toggleClass('fa-angle-up');

		// Close all opens
		$('.click-to-open > a.open').not( $(this) ).removeClass('open').find('.fa-angle-down.fa-angle-up').removeClass('fa-angle-up');

	});


	// Choose to close dropdowns
	$(document).on('click', '.click-to-open.choose-to-close > a:not(.editing) + ul a:not(.has-sub)', function(e) {

		// Close all opens
		$('.click-to-open > a.open').removeClass('open').find('.fa-angle-down.fa-angle-up').removeClass('fa-angle-up');;

	});


	// Blur on click-to-open dropdowns
	document.addEventListener('click', function(e) {

		
		if ( !$(e.toElement).parents('.click-to-open').length ) {

			//console.log('BLUR FROM DROPDOWN', e.relatedTarget, e);

			// Close all opens
			$('.click-to-open > a.open').removeClass('open').find('.fa-angle-down.fa-angle-up').removeClass('fa-angle-up');;

		}


	});


	// Links with confirmation
	$(document).on('click', '[data-confirm]:not([data-action])', function(e) {

		var confirmation = $(this).attr('data-confirm');

		// Redownload exception
		if ( $(this).hasClass('redownload') && Pins.length == 0 ) return true;

		if ( confirmation != "" && confirm(confirmation) ) {

			return true;

		}

		e.preventDefault();
		return false;
	});


	// Close Modal
	$('.cancel-button').on('click', function(e) {

		var popup = $(this).parents('.popup-window').attr('id');

		closeModal('#' + popup);


		// Trial Expiration 
		if (popup == "trialexpired") {
	
			
			ajax('expired-notified', {
	
				'nonce'	: nonce
	
			}).done(function(result) {
	
				console.log('RESULT: ', result);
				if ( result.status != "success" ) console.error('ERROR: ', result);
	
			});


		}


		e.preventDefault();
		return false;

	});

	// Close Modal via Escape key
	$(document).keydown(function (e){

	    if( e.keyCode == 27 && $('.popup-window.active').length ) {

			console.log('CLOSE POPUP via ESC');
			$('.popup-window.active .cancel-button').trigger('click');

		    e.preventDefault();
		    return false;
	    }

	});


	// More Options Button on New Page/Project Modal
	$('.popup-window .option-toggler').on('click', function(e) {

		var popup = $(this).parents('.popup-window');


		popup.toggleClass('more-options');


		// if ( popup.hasClass('more-options') )
		// 	popup.find('.more-options-wrapper input').prop('disabled', false);
		// else
		// 	popup.find('.more-options-wrapper input').prop('disabled', true);


		e.preventDefault();
		return false;


	});


	// New project modal URL check
	$(document).on('submit', '#add-new .new-project-form', function(e) {

		var url = $(this).find('input[name="page-url"]').val();
		var design = $(this).find('input[name="design-upload"]').val();
		var project_ID = $(this).find('input[name="project_ID"]').val();
		var page_width = $(this).find('input[name="page_width"]').val();
		var page_height = $(this).find('input[name="page_height"]').val();
		var submit = $(this).find('.submitter');
		var wrapper = $(this).parents('form');


		if (design) {

			wrapper.addClass('uploading');

			// Start the process
			var uploadDesignProcessID = newProcess(true, "uploadDesignProcess");


			$.ajax({
				url: ajax_url+'?type=design-upload',
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

							submit.prop('disabled', true).text(percentComplete +'%');

							if (percentComplete == 100) submit.text('Opening');

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
					endProcess(uploadDesignProcessID);


					// Redirect
					window.location.href = "/revise/" + data.device_ID;


				},
				error: function(jqXHR, textStatus, errorThrown) {

					console.log('FAILED!!', errorThrown);

					
					// Finish the process !!!
					endProcess(uploadDesignProcessID);

				}
			});
			

			e.preventDefault();
			return false;
		}
		
		

		// Pages check in current project
		var pageExists = false;
		var pageFound = myPages.filter(function(page) {
			return urlStandardize(page.page_url, true) == urlStandardize(url, true) && page.project_ID == project_ID;
		});
		if (pageFound.length) {

			pageExists = true;
			//console.log('pageFound', pageFound);


			// Multiple
			if (pageFound.length > 1) {

				console.log('Multiple pages found in current project', pageFound);
				// Open page selector modal
				// ... !!!

			} else {

				console.log('Page found in current project', pageFound[0]);


				// Confirm: Should we add a new phase?
				if ( confirm("The URL you entered already has a page in this project. Should we add a new phase on this page?") ) {

					var newPhaseUrl = "/projects?new_phase="+ pageFound[0].page_ID +"&page_width="+page_width+"&page_height="+page_height;
					console.log('Redirect to a new phase URL...', newPhaseUrl);


					// Redirect
					window.location = newPhaseUrl;

				}

				e.preventDefault();

			}

		}



		// Pages check in other projects
		var pageExistsInOtherProjects = false;
		var pageFoundInOtherProject = myPages.filter(function(page) {
			return urlStandardize(page.page_url, true) == urlStandardize(url, true) && page.project_ID != project_ID;
		});
		if (!pageExists && pageFoundInOtherProject.length) {

			pageExistsInOtherProjects = true;
			//console.log('pageExistsInOtherProjects', pageFoundInOtherProject);


			// Multiple
			if (pageFoundInOtherProject.length > 1) {

				console.log('Multiple pages found in another projects', pageFoundInOtherProject);
				// Open page selector modal
				// ... !!!
	
			} else {

				console.log('Page found in another project', pageFoundInOtherProject[0]);


				// Confirm: Should we add a new phase?
				if ( confirm("The URL you entered already has a page in another project. Should we add a new phase on that page?") ) {

					var newPhaseUrlOtherProject = "/projects?new_phase="+ pageFoundInOtherProject[0].page_ID +"&page_width="+page_width+"&page_height="+page_height;
					console.log('Redirect to a new phase URL...', newPhaseUrlOtherProject);


					// Redirect
					window.location = newPhaseUrlOtherProject;

				}

				e.preventDefault();

			}

		}



		// Check other project domains
		var projectExists = false;
		var projectsFound = [];
		var pageFoundByDomainInOtherProjects = myPages.filter(function(page) {

			return getDomainName(page.page_url) == getDomainName(url) && page.project_ID != project_ID;

		});
		pageFoundByDomainInOtherProjects = removeDuplicates(pageFoundByDomainInOtherProjects, 'project_ID'); // Show only unique projects
		if (!pageExists && !pageExistsInOtherProjects && pageFoundByDomainInOtherProjects.length) {

			projectExists = true;
			//console.log('pageFoundByDomainInOtherProjects', pageFoundByDomainInOtherProjects);


			// Multiple
			if (pageFoundByDomainInOtherProjects.length > 1) {

				console.log('Multiple projects found with this domain', pageFoundByDomainInOtherProjects);
				// Open project selector modal
				// ... !!!
	
			} else {

				console.log('Project found with this domain', pageFoundByDomainInOtherProjects[0]);


				// Confirm: Should we add a new page in the found Project?
				if ( confirm("The URL you entered already has a project. Should we add a new page in that Project?") ) {

					console.log('Update the project number...');


					// Update the project id as new
					$(this).find('input[name="project_ID"]').attr('value', pageFoundByDomainInOtherProjects[0].project_ID);


				} else { // If not confirmed

					e.preventDefault();

				}

			}

		}



		// Recommend adding different project
		var newProject = false;
		var pageFoundNotImage = myPages.filter(function(page) {
			return page.page_url != "image" && page.project_ID == project_ID;
		});
		var pageFoundByDomain = myPages.filter(function(page) {
			return getDomainName(page.page_url) == getDomainName(url) && page.project_ID == project_ID;
		});
		if (!pageExists && !pageExistsInOtherProjects && !projectExists && !pageFoundByDomain.length && project_ID != "new" && project_ID != "autodetect" && pageFoundNotImage.length) {

			newProject = true;
			console.log('Recommend creating New Project', pageFoundByDomain);


			// Confirm: Should we add a new project for this URL?
			if ( confirm("The URL you entered doesn't look belong to this project. Should we create a new project for this URL?") ) {

				console.log('Redirect to a new project URL...');


				// Update the project id as new
				$(this).find('input[name="project_ID"]').attr('value', 'new');


			} else { // If not confirmed

				e.preventDefault();

			}


		} else {


			// ADD NEW PAGES WITHOUT ANY PROBLEM
			// e.preventDefault();
			// return false;


		}




		// console.log('pageExists', pageExists);
		// console.log('pageExistsInOtherProjects', pageExistsInOtherProjects);
		// console.log('projectExists', projectExists);
		// console.log('newProject', newProject);

		// e.preventDefault();
		// return false;




		console.log('URL: ', url);
		console.log('Project ID: ', project_ID);

	}).on('reset', '.new-project-form', function(e) {


		var form = $('.new-project-form');


		form.attr('data-page-type', 'url');

		form.find('input[name="page-name"]').val('').prop('required', false);
		form.find('.page-url input').prop('disabled', false);
		form.find('.page-options input').val('url');
		form.find('.submitter').text('ADD');


	});


	// New page/project inputs
	$(document).on('input', 'form.new-project-form input', function(e) {


		// Update the cat ID
		var currentForm = $(this).parents('form').first();
		var formCatID = currentForm.attr('data-cat-id');
		var forms = $('form[data-cat-id="'+ formCatID +'"]');
		var otherForm = $('form[data-cat-id="'+ formCatID +'"]').not(currentForm);

		// Input values
		var name = $(this).attr('name');
		var value = $(this).val();


		// If URL input
		if (name == "page-url") {


			// Protocol check
			if ( value.length > 8 && value.indexOf('://') === -1 ) {
				value = 'http://' + value;
				$(this).val(value);
			}


			// Show page modes
			if (value.length) forms.attr('data-url-entered', 'yes');
			else forms.attr('data-url-entered', 'no');


		}


		otherForm.find('input[name="'+ name +'"]:not([type="checkbox"]):not([type="radio"])').val(value);
		otherForm.find('input[type="radio"][name="'+ name +'"][value="'+ value +'"]').prop('checked', true);


	});


	// Add user toggle
	$('.new-member').click(function(e) {

		$(this).next().fadeToggle();

		e.preventDefault();
		return false;

	});


	// ADD NEW MODAL: New user
	$('.new-member + input').keydown(function (e) {

		var input = $(this);
		var type = $(this).attr('data-type');
		var userList = $('.shares.user.'+type);
		var emailList = $('.shares.email.'+type);
		var lists = $('.shares.'+type);

		function userTemplate_html(user_ID, userPhoto, userName, type, deletable) {


			// Default
			deletable = assignDefault(deletable, true);


			return '\
				<li class="'+ (!deletable ? 'undeletable' : '') +'" data-to="'+ user_ID +'">\
					'+ (deletable ? '<input type="hidden" name="'+type+'_shares[]" value="' + user_ID + '"/>' : '') +'\
					<picture class="profile-picture" ' + userPhoto + '>\
						'+userName+'\
					</picture>\
					<a href="#" class="remove-share" data-type="'+type+'" data-value="'+user_ID+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
				</li>\
			';

		}

		function emailTemplate_html(email, type, deletable) {


			// Default
			deletable = assignDefault(deletable, true);


			return '\
				<li class="'+ (!deletable ? 'undeletable' : '') +'" data-to="'+ email +'">\
					'+ (deletable ? '<input type="hidden" name="'+type+'_shares[]" value="'+email+'"/>' : '') +'\
					<span>\
						'+email+'\
						<a href="#" class="remove-share" data-type="'+type+'" data-value="'+email+'"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
					</span>\
				</li>\
			';

		}


	    if(e.keyCode == 13) {

		    input.prop('disabled', true);


			// Start the process
			var actionID = newProcess(true, "userCheck");

			ajax('user-check', {

				'email'	: input.val(),
				'nonce'	: nonce

			}).done(function(result) {


				var data = result.data;
				console.log('DATA: ', data);


				if ( data.status == "found" || data.status == "not-found" ) {


					if ( !lists.find('[value="'+data.share_to+'"]').length && !$('.shares.project').children('[data-to="'+data.share_to+'"]').length ) {


						// Add if not already exists
						if ( data.status == "found" )
							userList.append( userTemplate_html(data.user_ID, data.user_photo, data.user_name, type) );

						if ( data.status == "not-found" )
							emailList.append( emailTemplate_html(input.val(), type) );


					}



					// If project sharing
					if (type == "project") {


						// Remove from the page list
						$('.shares.page').children('[data-to="'+data.share_to+'"]').remove();

					}



					input.removeClass('error');
					input.val('');


				} else {

					input.addClass('error');

				}


				input.prop('disabled', false);
				input.focus();


				// Finish the process
				endProcess(actionID);


			});



		    e.preventDefault();
		    return false;
	    }

	});


	// ADD NEW MODAL: Delete selected shared person from the list
	$(document).on('click', '.shares a.remove-share', function(e) {


		var share = $(this).closest('li');
		var type = $(this).attr('data-type');
		var value = $(this).attr('data-value');


		// Remove the share
		share.remove();


		// Remove from page list
		if ( type == "project" && $('.shares > li.undeletable a.remove-share[data-value="'+value+'"]').length ) {

			$('.shares a.remove-share[data-value="'+value+'"]').closest('li').remove();

		}


		e.preventDefault();
		return false;

	});


	// ADD NEW MODAL: New screen
	$('#add-new .screen-add a').click(function(e) {

		var listed_screen = $(this).parent();
		var listed_screen_cat = $(this).parents('.screen-cat');

		var screen_id = $(this).attr('data-screen-id');
		var screen_width = $(this).attr('data-screen-width');
		var screen_height = $(this).attr('data-screen-height');
		var screen_cat_name = $(this).attr('data-screen-cat-name');
		var screen_cat_icon = $(this).attr('data-screen-cat-icon');


		var new_screen_html = '\
			<li>\
				<input type="hidden" name="screens[]" value="'+screen_id+'"/>\
				<i class="fa '+ screen_cat_icon +'" aria-hidden="true"></i> <span>'+screen_cat_name+' ('+screen_width+' x '+screen_height+')</span>\
				<a href="#" class="remove-screen"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
			</li>\
		';



		if (screen_id == 11) {

			new_screen_html = '\
				<li>\
					<input type="hidden" name="screens[]" value="'+screen_id+'">\
					<input type="hidden" name="page_width" value="'+screen_width+'">\
					<input type="hidden" name="page_height" value="'+screen_height+'">\
					<i class="fa '+ screen_cat_icon +'" aria-hidden="true"></i> <span>Current Screen (<span class="screen-width">'+screen_width+'</span> x <span class="screen-height">'+screen_height+'</span>)</span>\
					<a href="#" class="remove-screen"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
				</li>\
			';

		}




		$('.selected-screens').append(new_screen_html);


		listed_screen.hide();


		// Check if any other screen left in that category
		if ( !listed_screen.parent().children(':visible').length )
			listed_screen_cat.hide();


		// Show all the remove buttons
		$('.selected-screens a.remove-screen').show();


		e.preventDefault();

	});


	// ADD NEW MODAL: Delete selected screen from the list
	$(document).on('click', '.selected-screens a.remove-screen', function(e) {


		var listed_screen = $(this).parent();
		var listed_screen_cat = $(this).parents('.screen-cat');

		var screen = listed_screen.find('input');
		var screen_id = screen.attr('value');
		var screen_from_list = $('.screen-add > li > a[data-screen-id="'+screen_id+'"]').parent();


		// Show in the list
		screen_from_list.show();


		// Show the category
		screen_from_list.parents('.screen-cat').show();


		// Remove the screen
		listed_screen.remove();


		// Count the selected screens and if less than 2, hide that remover
		if ( $('.selected-screens > li').length < 2 ) {

			$('.selected-screens a.remove-screen').hide();

		}


		console.log('REMOVE', screen_id);


		e.preventDefault();
		return false;

	});


	// Plain text paste on content editable blocks
	$('[contenteditable]').on('paste',function(e) {


		e.preventDefault();

		var plain_text = (e.originalEvent || e).clipboardData.getData('text/plain');

		if(typeof plain_text !== 'undefined')
			document.execCommand('insertText', false, plain_text);

		console.log('PASTED: ', plain_text);


	});


	// ACTIONS - Archive, delete, recover, rename, ...
	$(document).on('click', '[data-action]', function(e) {


		// Item details
		var item = $(this).parents('.item').first();
		var object_ID = item.attr('data-id') || null;
		var object_type = item.attr('data-type') || null;
		var firstParameter = item.attr('data-parameter') || null;
		var secondParameter = item.attr('data-second-parameter') || null;
		var thirdParameter = item.attr('data-third-parameter') || null;


		// Action details
		var action = $(this).attr('data-action') || null;
		var confirmText = $(this).attr('data-confirm') || false;


		// When renaming
		if (action == "rename") {

			var parent_item = item.find('.name-field').first();
			var input = item.find('input.edit-name').first();
			firstParameter = input.val();

			parent_item.toggleClass('editing');

			input.focus();

			//item.find('.dropdown > ul').hide();

			// If the same text
			if ( input.val() == input.attr('value') ) {
				e.preventDefault();
				return false;
			}

		}


		// If confirmed, send data
		if ( !confirmText || confirm(confirmText) )
			doAction(action, object_type, object_ID, firstParameter, secondParameter, thirdParameter);


		e.preventDefault();
		return false;

	});


	// Modal Opener
	$(document).on('click', '[data-modal]', function(e) {


		var modalName = $(this).attr('data-modal');
		var modal = $('#' + modalName + '.popup-window');
		if ( !modal.length ) return;
		if (modalName == "upgrade" && modal.attr('data-current-plan') == "Enterprise") return;

		var dataType = $(this).attr('data-type');
		var object_ID = $(this).attr('data-id');
		var objectName = $(this).attr('data-object-name');
		var iamOwner = $(this).attr('data-iamowner');


		// Update the modal data
		modal.attr('data-type', dataType);
		modal.attr('data-id', object_ID);
		modal.attr('data-iamowner', iamOwner);


		// Update the fields
		modal.find('.data-type').text(dataType);
		modal.find('.data-name').text(objectName);
		modal.find('.data-id').text(object_ID);


		// SHARE MODALS
		if (modalName == "share") {


			var input = modal.find('#share-email');


			// Reset the new access selector
			$('#share .new-access-type-selector > li').removeClass('selected');
			$('#share .new-access-type-selector > li:first-child').addClass('selected');
			var accessLabel = $('#share .new-access-type-selector > li:first-child > a').text();
			$('#share .new-access-type').text(accessLabel);


			// Reset the input and button
			input.attr('data-add-type', dataType);
			input.removeClass('error');
			input.val('');
			$('#share button.add-member').prop('disabled', true);


			updateShares();


		}


		// NEW PAGE/PROJECT MODALS
		if (modalName == "add-new") {


			var thisBlock = $(this).parents('.block');


			// Project ID
			modal.find('input[name="project_ID"]').attr('value', object_ID);		
		
		
			// Update the current category name
			var catName = $(this).parents('.category').find('.cat-separator .name').text();
			modal.find('.to').html('');
			if (catName != "Uncategorized" && catName != "")
				modal.find('.to').html("To <b>"+ catName +"</b> Section");
		
		
			// Print the project name
			if (!thisBlock.length && catName == "" && dataType == "page")
				modal.find('.to').html("To <b>"+ objectName +"</b> Project");

			
			// Category ID input update
			var catID = $(this).parents('.category').attr('data-id') || 0;
			modal.find('form').attr('data-cat-id', catID);
			modal.find('input[name="category"]').attr('value', catID);


			// Order number input update
			var orderNumber = $(this).parents('.category').attr('data-order') || 0;
			modal.find('input[name="order"]').attr('value', ( parseInt(orderNumber) + 1 ));


		}


		// Open the modal
		openModal(modalName);


		e.preventDefault();
		return false;

	});


	// Copy Share Links
	$(document).on('mouseover', '#share .link', function(e) { console.log('Clicked');

		$('#share .link').attr('data-tooltip', 'Click to Copy');

	}).on('click', '#share .link', function(e) { console.log('Clicked');

		copyToClipboard('#share .link .value');
		$('#share .link').attr('data-tooltip', 'Copied!');

		e.preventDefault();
		return false;
	});


	// Feedback Modal
	var starInfo = [
		"I cannot use it :(",
		"Found some issues :/",
		"Not bad :|",
		"Good :)",
		"Everything is great ;)"
	];
	$(document).on('change', '#feedback select[name="feedback-type"]', function(e) {

		var modal = $('#feedback');
		var feedbackType = $(this).val();


		// Update the data
		modal.attr('data-feedback-type', feedbackType);


		// Comment requirement
		if (feedbackType == "feedback") modal.find('textarea').removeAttr('required');
		else modal.find('textarea').attr('required', "true");



		e.preventDefault();
		return false;

	}).on('input', '#feedback textarea[name="feedback"]', function(e) {

		var modal = $('#feedback');
		var feedback = $(this).val();


		// Update the data
		modal.find('.current-length').text( feedback.length );



		e.preventDefault();
		return false;

	}).on('mouseover', '#feedback .stars > .fa-star', function(e) {

		var modal = $('#feedback');
		var star = parseInt($(this).attr('data-value'));
		var starWrapper = modal.find('.star-info');


		// Update start info
		starWrapper.text( starInfo[star - 1] );


		// Update the data
		modal.find('.stars > .fa-star').removeClass('fas far').each(function(i) {

			if (i+1 <= star) $(this).addClass('fas');
			else $(this).addClass('far');

		});



		e.preventDefault();
		return false;

	}).on('mouseout', '#feedback .stars > .fa-star', function(e) {

		var modal = $('#feedback');
		var star = parseInt( modal.find('[name="stars"]').val() );
		var starWrapper = modal.find('.star-info');


		// Update start info
		starWrapper.text( starInfo[star - 1] );


		// Update the data
		modal.find('.stars > .fa-star').removeClass('fas far').each(function(i) {

			if (i+1 <= star) $(this).addClass('fas');
			else $(this).addClass('far');

		});



		e.preventDefault();
		return false;

	}).on('click', '#feedback .stars > .fa-star', function(e) {

		var modal = $('#feedback');
		var star = parseInt($(this).attr('data-value'));


		modal.find('[name="stars"]').attr('value', star);


		e.preventDefault();
		return false;

	}).on('submit', '#feedback form', function(e) {

		var modal = $('#feedback');
		var form = $(this);
		var input = form.find('input[type="submit"]');


		console.log('FORM SUBMITTED');



		// Rename the input and disable
		input.attr('value', "SENDING...").prop('disabled', true);


		// Start the process
		var feedbackSubmitProcessID = newProcess(true, "feedbackSubmitProcess");


		$.ajax({
			url: ajax_url+'?type=feedback',
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

						editProcess(feedbackSubmitProcessID, percentComplete);
						//if (percentComplete == 100) submit.val('Opening');

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


				// Rename the input and enable
				input.attr("value", 'SEND FEEDBACK').prop('disabled', false);



				if (status != "success") {

					console.error('ERROR: ', status, data, imageUrl, textStatus, jqXHR);
					return false;

				}


				console.log('SUCCESS!', imageUrl, data, textStatus, jqXHR);


				// Show sent message
				modal.attr('data-sent', 'yes');


				// Finish the process
				endProcess(feedbackSubmitProcessID);


			},
			error: function(jqXHR, textStatus, errorThrown) {

				console.log('FAILED!!', errorThrown);


				// Rename the input and enable
				input.attr("value", 'SEND FEEDBACK').prop('disabled', false);

				
				// Finish the process !!!
				endProcess(feedbackSubmitProcessID);

			}
		});





		e.preventDefault();
		return false;

	});


	// Trial start modal
	if ( getParameterByName('trialstarted') !== null ) openModal('trialstarted');
	//removeQueryArgFromCurrentUrl('trialstarted');

	if ( getParameterByName('welcome') !== null ) openModal('welcome');
	//removeQueryArgFromCurrentUrl('welcome');

	if ( getParameterByName('trialreminder') !== null ) openModal('trialreminder');
	//removeQueryArgFromCurrentUrl('trialreminder');

	if ( getParameterByName('trialexpired') !== null || (trialStarted == "yes" && trialExpired && !trialExpiredNotified) ) openModal('trialexpired');
	//removeQueryArgFromCurrentUrl('trialexpired');

	if ( getParameterByName('trialcanceled') !== null ) openModal('trialcanceled');
	//removeQueryArgFromCurrentUrl('trialcanceled');

	if ( getParameterByName('limit-warning') !== null ) openModal('limit-warning');
	//removeQueryArgFromCurrentUrl('trialcanceled');


	// Create a project
	$('.create-project').click(function(e) {

		closeModal();

		$('#url-0').parents('.box').addClass('pulse');
		$('#url-0').focus();

	});


	// SHARE MODAL: Share input
	$('#share #share-email').on('keyup', function() {

		var inputVal = $(this).val();

		if ( inputVal.length > 0 ) {

			$('#share button.add-member').prop('disabled', false);

		} else {

			$('#share button.add-member').prop('disabled', true);

		}

	}).on('keydown', function (e) {


	    if(e.keyCode == 13) {

			addshare( $(this) );

		    e.preventDefault();
		    return false;
	    }

	});


	// SHARE MODAL: Add Member Button
	$('#share .add-member').on('click', function(e) {

		addshare( $('#share #share-email') );

		e.preventDefault();
		return false;
	});


	// SHARE MODAL: New member access type change
	$('#share .new-access-type-selector > li > a').on('click', function(e) {

		var newType = $(this).attr('data-type');
		var newLabel = $(this).text();


		// Update the share input
		$('#share #share-email').attr('data-add-type', newType);


		// Update the label
		$('#share .new-access-type').text(newLabel);


		// Update the selected item
		$('#share .new-access-type-selector > li').removeClass('selected');
		$(this).parent().addClass('selected');


		e.preventDefault();
		return false;
	});


	// Error inputs when typing
	$('input').keydown(function() {

		$(this).removeClass('error');

	});


	// Range sliders
	$('input[type="range"]').on('input change', function() {


		var value = $(this).val();
		//console.log('Changed', value);

		// Find the percentage
		var percentage = parseInt(value * 100);


		$(this).next('.percentage').text(percentage);


	});


	// Notifications
	$('.notification-opener').click(function(e) {

		$(this).toggleClass('open');

		e.preventDefault();
	});


	// Refresh Notifications
	$(document).on('click', '.refresh-notifications', function(e) {


		if ( $(this).hasClass('.notification-opener') && ! $(this).hasClass('open') ) return false;


		getNotifications(true);


		e.preventDefault();
	});


	// Load More Notifications
	$(document).on('click', '.more-notifications > a', function(e) {


		var offset = $(this).attr('data-offset');


		moreNotifications(offset);


		e.preventDefault();
	});


	// Start auto checking notifications
	if (loggedIn) {
		getNewNotificationCount();
		startNotificationAutoRefresh();
	}


	// Alert auto removal
    setTimeout(function() {

		dismissAlert( $('.alerts > .alert.success') );

	}, 4000);


	// Dismiss an alert
	$(document).on('click', '.alert > .close', function() {

		dismissAlert( $(this).parent() );

	});



	// Avatar Upload
	// Uploader
	$(document).on('change', '.avatar-upload', function() {


	    var userID = $('.avatar-changer').attr('data-id');
		var maxSize = $(this).attr('data-max-size');


	    var reader = new FileReader();
	    reader.onload = function(event) {


			// Temp data URL
			var imageSrc = event.target.result;


			// Apply the change
			$('.profile-picture[data-type="user"][data-id="'+ userID +'"]').attr('style', 'background-image: url('+imageSrc+');');
			$('.profile-picture[data-type="user"][data-id="'+ userID +'"]').addClass('loading');


			// Submit data
			$('#avatar-form').submit();


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


	$(document).on('submit', '#avatar-form', function(e) {


	    var userID = $(this).find('.avatar-changer').attr('data-id');


		$.ajax({
			url: ajax_url+'?type=avatar-upload&user_ID='+userID,
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
				
				var status = data.status;

				if (status != "success") {

					console.error('ERROR: ', status, data, textStatus, jqXHR);
					return false;

				}

				console.log('SUCCESS!', data, textStatus, jqXHR);

				// Update the image
				$('.profile-picture[data-type="user"][data-id="'+ userID +'"]').attr('style', 'background-image: url('+ data.new_url +');').removeClass('loading');

			},
			error: function(jqXHR, textStatus, errorThrown) {

				console.log('FAILED!!', errorThrown);

				$('.profile-picture[data-type="user"][data-id="'+ userID +'"]').removeClass('loading');

			}
		});


		e.preventDefault();

	});



	// CLIENT SIDE UPLOADER
	$(document).on('change', '.design-upload', function() {

		var formCatID = $(this).parents('form').attr('data-cat-id');
		var form = $('form[data-cat-id="'+ formCatID +'"]');
		var maxSize = $(this).attr('data-max-size');


	    var reader = new FileReader();
	    reader.onload = function(event) {


			// Temp data URL
			var imageSrc = event.target.result;


			form.attr('data-page-type', 'image');

			form.find('.selected-image img').attr('src', imageSrc);

			form.find('.page-url input').prop('disabled', true);
			form.find('.page-options input').val('image');
			form.find('input[name="page-name"]').prop('required', true).focus();
			form.find('.submitter').text('UPLOAD');


			// Reset the devices
			form.find('.selected-screens').html('\
				<li>\
					<input type="hidden" name="screens[]" value="11"/>\
					<input type="hidden" name="page_width" value="1440"/>\
					<input type="hidden" name="page_height" value="900"/>\
					<i class="fa fa-window-maximize" aria-hidden="true"></i> <span>Current Window (<span class="screen-width">1440</span> x <span class="screen-height">900</span>)</span>\
					<a href="#" class="remove-screen" style="display: none;"><i class="fa fa-times-circle" aria-hidden="true"></i></a>\
				</li>\
			');

			form.find('.screen-adder .screen-cat:not([data-screen-cat-id="5"]), .screen-adder .screen').show();



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


	// ADD IMAGE DEVICE
	$(document).on('click', '[data-page-type="image"] ul.screen-adder > li > a', function(e) {


		var first_screen_ID = parseInt( $(this).attr('data-first-screen-id') );
		var found_project_ID = typeof project_ID === 'undefined' ? parseInt( $(this).parents('[data-project-id]').attr('data-id') ) : project_ID;
		var found_page_ID = typeof page_ID === 'undefined' ? parseInt( $(this).parents('[data-id]').attr('data-id') ) : page_ID;
		var found_phase_ID = typeof phase_ID === 'undefined' ? parseInt( $(this).parents('[data-phase-id]').attr('data-phase-id') ) : phase_ID;


		$('#image-device-adder input[name="project_ID"]').val(found_project_ID);
		$('#image-device-adder input[name="page_ID"]').val(found_page_ID);
		$('#image-device-adder input[name="phase_ID"]').val(found_phase_ID);
		$('#image-device-adder input[name="screens[]"]').val(first_screen_ID);
		$('#image-device-adder input[name="design-upload"]').click();
		console.log(first_screen_ID);
		

		e.preventDefault();
		return false;

	});


	// ADD IMAGE PHASE
	$(document).on('click', '[data-page-type="image"] a.add-phase', function(e) {


		var found_project_ID = typeof project_ID === 'undefined' ? parseInt( $(this).parents('[data-project-id]').attr('data-project-id') ) : project_ID;
		var found_page_ID = typeof page_ID === 'undefined' ? parseInt( $(this).parents('[data-type="page"][data-id]').attr('data-id') ) : page_ID;

		$('#image-device-adder input[name="project_ID"]').val(found_project_ID);
		$('#image-device-adder input[name="page_ID"]').val(found_page_ID);
		$('#image-device-adder input[name="phase_ID"]').val('');
		$('#image-device-adder input[name="screens[]"]').val(11); // Custom one
		$('#image-device-adder input[name="design-upload"]').click();
		

		e.preventDefault();
		return false;

	});


	// ADD IMAGE DEVICE AUTO-SUBMIT
	$(document).on('change', '#image-device-adder input[name="design-upload"]', function() {

		var form = $(this).parents('form');
		var maxSize = $(this).attr('data-max-size');


	    var reader = new FileReader();
	    reader.onload = function(event) {


			// Temp data URL
			var imageSrc = event.target.result;


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

	// Image submission
	$(document).on('submit', '#image-device-adder', function(e) {


		// Start the process
		var uploadDesignProcessID = newProcess(true, "uploadDesignProcess");


		$.ajax({
			url: ajax_url+'?type=design-upload',
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

						if (percentComplete < 100) editProcess(uploadDesignProcessID, percentComplete);
						//if (percentComplete == 100) submit.val('Opening');

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
				endProcess(uploadDesignProcessID);


				// Redirect
				window.location.href = "/revise/" + data.device_ID;


			},
			error: function(jqXHR, textStatus, errorThrown) {

				console.log('FAILED!!', errorThrown);

				
				// Finish the process !!!
				endProcess(uploadDesignProcessID);

			}
		});
		

		e.preventDefault();
		return false;

	});


	$(document).on('submit', '#notify-me-form', function(e) {

		var form = $(this);
		var email = form.find('input[name="notify-email"]').val();
		var feature = form.find('input[name="feature"]').val();


		console.log('NOTIFYING: ', email, feature);


		form.attr('data-status', 'submitting');

		ajax('notify-me', {
	
			'email' : email,
			'feature' : feature,
			'nonce'	: nonce

		}).done(function(result) {

			console.log('RESULT: ', result);
			if ( result.status != "success" && result.status != "already-exists" ) console.error('ERROR: ', result);

			form.attr('data-status', result.status);

		});



		e.preventDefault();
	});


});



// When everything is loaded
$(window).on("load", function (e) {


});


// Update add new form info
function updateAddNewInfo(cloneForm) {



	var modal = $('#add-new');
	
	
	// Category ID input update
	var catID = cloneForm.attr('data-cat-id') || 0;
	modal.find('form').attr('data-cat-id', catID);
	modal.find('input[name="category"]').attr('value', catID);


	// Order number input update
	var orderNumber = cloneForm.parents('.category').attr('data-order') || 0;
	modal.find('input[name="order"]').attr('value', ( parseInt(orderNumber) + 1 ));


	// URL input update
	var urlEntered = cloneForm.find('input[name="page-url"]').val() || "";
	modal.find('input[name="page-url"]').val(urlEntered).trigger('input');


	// Name input update
	var nameEntered = cloneForm.find('input[name="page-name"]').val() || "";
	modal.find('input[name="page-name"]').val(nameEntered);


	// Selected Image
	var selectedImageUrl = cloneForm.find('.selected-image img').attr('src') || "";
	modal.find('form.new-project-form .selected-image img').attr('src', selectedImageUrl);


	// Page type
	var pageType = cloneForm.attr('data-page-type') || "url";
	modal.find('form.new-project-form').attr('data-page-type', pageType);


}


// Update shares
function updateShares() {


	console.log('Updating the shares...');


	var modal = $('#share');
	var currentUserId = modal.attr('data-currentuser-id');


	// Update the modal data
	var dataType = modal.attr('data-type');
	var object_ID = parseInt(modal.attr('data-id'));
	var iamOwner = modal.attr('data-iamowner');


	// Clean the old data
	modal.find('.members').html('<div class="xl-center comments-loading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Loading...</span></div>');


	// Bring the users from DB
	ajax('shares-get', {

		id : object_ID,
		dataType : dataType

	}).done(function(result) {


		var users = result.users;
		//console.log('RESULTS:', result);


		// Clean the wrappers
		modal.find('.members').html('');

		$(users).each(function(i, user) {

			$('.people[data-type="'+ user.type +'"][data-id="'+ user.object_ID +'"]').html('');

		});




		// Append the users
		var ownerID;
		$(users).each(function(i, user) {

			if (user.mStatus == "owner") ownerID = user.user_ID;
			var skipUser = user.mStatus == "projectowner" && ownerID == user.user_ID;


			// Modal info
			if (!skipUser) {

				modal.find('.members').append(
					new_modal_shared_member(user.mStatus, user.email, user.fullName, user.nameAbbr, user.userImageUrl, user.user_ID, dataType, user.type, currentUserId, user.sharer_user_ID, user.object_ID, object_ID)
				);

			}


			// Add to the people
			$('.people[data-type="'+ user.type +'"][data-id="'+ user.object_ID +'"]').append(
				boxMemberTemplate(user.mStatus, user.email, user.fullName, user.nameAbbr, user.userImageUrl, user.user_ID)
			);


		});



	}).fail(function(result) {


		console.log('FAILED:', result);


	});


	console.log('Shares updated');

}


// Add a share
function addshare() {


	var modal = $('#share');
	var type = modal.attr('data-type');
	var object_ID = modal.attr('data-id');
	var input = modal.find('#share-email');
	var addType = input.attr('data-add-type');
	var nonce = "";


	console.log('ADDING A SHARE: ', type, object_ID, input.val());


	// Disable the input
	input.prop('disabled', true);


	// Start the process
	var actionID = newProcess(true, type+"Share");

	ajax('share', {

		'data-type'	: type,
		'object_ID'	: object_ID,
		'email'		: input.val(),
		'add-type' 	: addType,
		'nonce'		: nonce

	}).done(function(result) {


		var data = result.data;
		console.log(result);


		// If user added
		if ( data.status == "added" ) {


			// Update the shares list
			updateShares();


			// Reset the input and button
			input.removeClass('error');
			input.val('');
			$('#share button.add-member').prop('disabled', true);


			// Scroll to the bottom
			modal.find('.scrollable-content').animate({
				scrollTop: 999
			}, "slow");


		} else if ( data.status == "invalid-email" ) {

			input.addClass('error');

		} else {

			input.addClass('error ' + data.status);

		}


		// Reactivate the input
		input.prop('disabled', false);
		input.focus();



		// Finish the process
		endProcess(actionID);

	});


}


// Do an action
function doAction(action, object_type, object_ID, firstParameter, secondParameter, thirdParameter, nonce) {


	// Defaults
	firstParameter = assignDefault(firstParameter, null);
	secondParameter = assignDefault(secondParameter, null);
	thirdParameter = assignDefault(thirdParameter, null);
	nonce = assignDefault(nonce, "");


	// Start progress bar action
	var preventRefreshing = action != "remove";
	var actionID = newProcess(preventRefreshing, object_type+":"+action);

	// AJAX Send data
	ajax('data-action', {
		'ajax' 			  : true,
		'action' 		  : action,
		'data-type' 	  : object_type,
		'id' 			  : object_ID,
		'firstParameter'  : firstParameter,
		'secondParameter' : secondParameter,
		'thirdParameter'  : thirdParameter,
		'nonce' 		  : nonce
	}).done(function(result) {


		var data = result.data;
		var items = $('.item[data-type="'+object_type+'"][data-id="'+object_ID+'"]');


		console.log("RESPONSE: ", data, object_type, object_ID);


		// Progressbar Update
		if ( data.status == "successful" || data.status == "fail-m" ) {


			if (action == "rename") {

				items.find('input.edit-name[data-type="'+object_type+'"][data-id="'+object_ID+'"]').attr('value', firstParameter );
				items.find('.name[data-type="'+object_type+'"][data-id="'+object_ID+'"]').text( firstParameter );
				items.children('.name').text( firstParameter );


			} else if (action == "archive" || action == "delete" || action == "remove" || action == "recover") {


				// Page redirection
				if (action == "remove" && firstParameter == "redirect") {

					console.log('GO TO URL: ', secondParameter);
					window.open(secondParameter, "_self");
					return true;

				}
					


				// Hide the item
				if ( object_type != "projectcategory" && object_type != "pagecategory" ) {

					items.remove();


					// Update limits
					var limitWrapper = $('.'+ object_type + 's-limit');
					var currentLimit = limitWrapper.find('.current');
					var maxLimit = parseInt( limitWrapper.find('.max').text() );
					if ( action == "remove" && currentLimit.length ) {


						var newLimit = parseInt( currentLimit.text() ) - 1;
						currentLimit.text( newLimit );
						if ( newLimit < maxLimit ) limitWrapper.removeClass('exceed');


						// Update the add new blocks
						if (typeof addNewPageButtons === "function") addNewPageButtons();


						// Refresh the page
						if (
							(object_type == "project" || 
							object_type == "page" || 
							object_type == "phase" || 
							object_type == "device") && firstParameter != "autoremove"
						) location.reload();


					}


					// Remove from myPages variable
					if (object_type == "page") {

						var pageFound = myPages.find(function(page) { return page.page_ID == object_ID; });

						if (pageFound) {

							var pageIndex = myPages.indexOf(pageFound);
							myPages.splice(pageIndex, 1);

						}

					}

				}


				// If action on categories
				if ( object_type == "projectcategory" || object_type == "pagecategory" ) {


					// Move the blocks to the Uncategorized section
					items.find('.block').attr('data-cat-id', 0).appendTo('.category[data-id="0"] .blocks');


					// Hide the item
					items.remove();


					// Update the add new blocks
					addNewPageButtons();

				}


			} else if (action == "changeshareaccess" || action == "unshare" || action == "makeownerof") {


				// Update the shares list again
				updateShares();


				// Object ownership
				if (action == "makeownerof") {
					$('#share').attr('data-iamowner', 'no');
					$('[data-type="'+ object_type +'"][data-id="'+ object_ID +'"][data-modal="share"]').attr('data-iamowner', 'no');
				}


				// Refresh the page when removing access of myself
				if ( action == "unshare" && object_ID == user_ID ) location.reload();


			} else {

				console.log(action + ' DONE');

			}


			// End the process
			endProcess(actionID);


		} else { // UNSUCCESSFUL


			if (action == "rename") {

				item.find('.name-field').addClass('editing ' + data.status);

			}


		}


	}).fail(function(error) {

		console.log('FAILED: ', error);

	});


}


// Get notifications
function getNotifications(markAsRead) {


	// Default
	markAsRead = assignDefault(markAsRead, false);


	console.log('Getting notifications...');
	stopNotificationAutoRefresh();


	$('.notifications > ul').addClass('loading').html('<li><i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Notifications are loading...</span></li>');



	ajax('notifications-get', {

		'offset' : 0,
		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);
		var notificationsHTML = result.notifications;


		// Apply to DOM
		$('.notifications > ul').html(notificationsHTML).removeClass('loading');


		// Mark all as read
		if (markAsRead && $('.notifications > ul > li.new').length) {


			// After 1 seconds, mark all read
			setTimeout(function() {

				if ( $('.notification-opener').hasClass("open") ) readNotifications();

			}, 1000);


		}


		// Restart the auto refresher
		startNotificationAutoRefresh();


	}).fail(function(e) {

		console.log('ERROR: ', e);
		startNotificationAutoRefresh();

	});

	return true;

}


// Get more notifications
function moreNotifications(offset) {


	// Default
	offset = assignDefault(offset, 0);


	console.log('Loading more notifications...');


	$('.notifications .more-notifications').html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i><span>Notifications are loading...</span>');


	ajax('notifications-get', {

		'offset' : parseInt(offset),
		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);
		var notificationsHTML = result.notifications;


		// Remove the loading text
		$('.notifications .more-notifications').remove();


		// Apply to DOM
		$('.notifications > ul').append(notificationsHTML);


		// Check the new data for errors
		if ( !$('.notifications > ul > li[data-offset="'+ offset +'"]:not(.error)').length ) {

			// Refresh the data
			if ( $('.notifications > ul > li.more-notifications').length )
				$('.notifications > ul > li.more-notifications > a').click();

		}



	}).fail(function(e) {


		$('.notifications .more-notifications').html('Notifications couldn\'t be loaded.');
		console.log('ERROR: ', e);

	});

	return true;

}


// Mark "read" all notifications
function readNotifications() {


	var IDsToRead = [];


	// Select all the notifications that's shown now
	$('.notifications > ul > li.new[data-type="notification"]').each(function() {

		var notification_ID = $(this).attr('data-id');

		IDsToRead.push(notification_ID);

	});


	// Don't send if no new notifications
	if (IDsToRead.length == 0) return true;


	// Send data
	ajax('notifications-read', {

		'notification_IDs' : IDsToRead,
		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);


		var currentNotifNumber = parseInt( $('.notification-opener .notif-no').text() );
		var newNotifNumber = currentNotifNumber - IDsToRead.length;


		// Update the count
		$('.notification-opener .notif-no').text(newNotifNumber).addClass( newNotifNumber == 0 ? "hide" : "" );


	}).fail(function(e) {

		console.log('ERROR: ', e);

	});

	return true;


}


// Get notification count
function getNewNotificationCount() {


	ajax('notifications-get-count', {

		'nonce'	: nonce

	}).done(function(result) {


		console.log('RESULT: ', result);

		var newCount = result.newcount;


		// Update the count
		$('.notification-opener .notif-no').text(newCount);
		if (newCount == 0) $('.notification-opener .notif-no').addClass('hide');
		else $('.notification-opener .notif-no').removeClass('hide');


		// Add the "Load new notifications" button
		var newButton = '<a href="#" class="refresh-notifications">'+ newCount +' New Notification'+ (newCount > 1 ? "s" : "") +'</a>';
		if (newCount > 0) {


			if ( !$('.notifications > ul > li.refresh-notifications').length )
				$('.notifications > ul:first-child').prepend('<li class="refresh-notifications"></li>');

			$('.notifications > ul > li.refresh-notifications').html(newButton);


		}


	}).fail(function(e) {

		console.log('ERROR: ', e);

	});

	return true;


}


// Start auto-refresh notifications
function startNotificationAutoRefresh() {

	if (!loggedIn) return false;

	console.log('AUTO-REFRESH NOTIFICATIONS STARTED');

	notificationAutoRefreshTimer = setInterval(function() {

		console.log('Auto checking the notifications...');


		// Abort the latest request if not finalized
		if(notificationAutoRefreshRequest && notificationAutoRefreshRequest.readyState != 4) {
			console.log('Latest request aborted');
			notificationAutoRefreshRequest.abort();
		}


		// Get the notification count
		getNewNotificationCount();


	}, notificationAutoRefreshInterval);

}


// Stop auto-refresh
function stopNotificationAutoRefresh() {

	console.log('AUTO-REFRESH NOTIFICATIONS STOPPED');

	if (notificationAutoRefreshRequest) notificationAutoRefreshRequest.abort();

	clearInterval(notificationAutoRefreshTimer);

}


// Show an alert
function showAlert(alert_ID) {

	var alert = $('.alerts, .alerts > #' + alert_ID);
	alert.removeClass('hidden');

	if ( alert.hasClass('autoclose') ) {

		setTimeout(function() {

			alert.addClass('hidden');

		}, 4000);

	}

}


// Dismiss alerts
function dismissAlert(selector) {

    selector.fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });

}




// MODALS:
// Open modal
function openModal(modalName) {


	var modal = $('#' + modalName + '.popup-window');
	if (!modal.length) return false;
	if (typeof currentPinType === 'undefined') var currentPinType = "screen";

	
	if (modalName == "limit-warning") {


		// Update the limitations
		$('#limit-warning')
			.attr('data-current-pin-mode', currentPinType)
			.attr('data-allowed-live-pin', limitations.current.pin)
			.attr('data-allowed-comment-pin', limitations.current.commentpin)
			.attr('data-allowed-phase', limitations.current.phase);


		// Update the content
		var limitText = "";
		if (currentPinType == "live" || currentPinType == "style")
			limitText = "<b>You have reached your live pin limit.</b><br>To be able to continue changing content of the page, please upgrade your account.";

		if (currentPinType == "comment")
			limitText = "<b>You have reached your comment pin limit.</b><br>To be able to continue adding comment pins, please upgrade your account.";

		if (currentPinType == "browse")
			limitText = "<b>You have reached your Page/Phase limit.</b><br>To be able to continue adding pages/phases, please upgrade your account.";

		if (currentPinType == "screen")
			limitText = "<b>You have reached your Screen limit.</b><br>To be able to continue adding new screen sizes, please upgrade your account.";

		if (limitations.current.pin == "0" && limitations.current.commentpin == "0" && limitations.current.page == "0")
			limitText = "<b>You have reached your account limits.</b><br>To be able to continue adding pins, please upgrade your account.";


		// Update the text
		$('#limit-warning .limit-text').html(limitText);


	}


	// FEEDBACK MODALS
	if (modalName == "feedback") {

		// Limitations
		var maxLength = modal.find('textarea[name="feedback"]').attr('maxlength');
		modal.find('.current-limit').text(maxLength);

		// Reset
		modal.find('form').trigger('reset');
		modal.find('.current-length').text('0');
		modal.attr('data-feedback-type', 'feedback');
		modal.find('.fa-star').removeClass('far').addClass('fas');
		modal.find('input[name="stars"]').attr('value', 5);
		modal.find('.star-info').text("Excellent");
		modal.attr('data-sent', 'no');

	}


	// NOTIFY MODALS
	if (modalName == "notifyme") {

		// Reset
		modal.find('#notify-me-form').attr('data-status', '');

	}


	// Close other modals
	$('.popup-window').removeClass('active');


	// Open the modal
	modal.addClass('active');
	$('body').addClass('popup-open');


	// Find lazy sources
	modal.find('[data-src]').each(function() {

		var src = $(this).attr('data-src');
		$(this).attr('src', src);

	});


	// Focus the element
	setTimeout(function() {

		if (modal.find('[autofocus]').length) modal.find('[autofocus]').focus();

	}, 500);


	return true;
}

// Close modal
function closeModal(modalElement) {

	modalElement = assignDefault(modalElement, ".popup-window");

	$(modalElement).removeClass('active');
	$('body').removeClass('popup-open');


	// Find lazy sources
	$(modalElement).find('[src]').each(function() {

		var src = $(this).attr('src');
		$(this).attr('data-src', src).removeAttr('src');

	});

}



// TEMPLATES
function boxMemberTemplate(mStatus, email, fullName, nameAbbr, userImageUrl, user_ID) {

	var printPic = 'style="background-image: url('+ userImageUrl +');"';
	var ownerBadge = ''; // !!!

	if (mStatus != 'email' ) email = '('+email+')';
	if (mStatus == 'email' ) nameAbbr = '<i class="fa fa-envelope" aria-hidden="true"></i>';
	if (mStatus == 'email' ) user_ID = email;
	if (mStatus == 'owner' ) ownerBadge = '<span class="owner-badge">Owner</span>';
	if (userImageUrl == "" || userImageUrl == null) printPic = "";

	return '\
		<picture class="profile-picture" '+printPic+' data-tooltip="'+fullName+'" data-type="user" data-id="'+user_ID+'">\
			<span>'+nameAbbr+'</span>\
		</picture>\
	';

}

function new_modal_shared_member(mStatus, email, fullName, nameAbbr, userImageUrl, user_ID, dataType, type, currentUserId, sharer_user_ID, object_ID, page_ID) {

	//console.log(mStatus, email, fullName, nameAbbr, userImageUrl, user_ID, dataType, type, currentUserId, sharer_user_ID, object_ID, page_ID);

	var printPicture = userImageUrl != null ? "style='background-image: url("+ userImageUrl +")'" : "";

	var shareText = "This " + dataType;
	if (mStatus == "owner") shareText = dataType + " Owner";
	if (mStatus == "projectowner") shareText = "Project Owner";
	if (mStatus == "project") shareText = "Whole Project";


	return '\
		<li class="wrap xl-flexbox xl-middle xl-gutter-16 member item" data-type="user" data-id="'+ user_ID +'" data-parameter="'+ type +'" data-second-parameter="'+ object_ID +'" data-third-parameter="'+ page_ID +'" data-share-status="'+ mStatus +'" data-itsme="'+ ( user_ID == currentUserId ? "yes" : "no" ) +'" data-my-share="'+ ( sharer_user_ID == currentUserId ? "yes" : "no" ) +'" data-confirmed="'+ ( email != "Not confirmed yet" ? "yes" : "no" ) +'">\
			<div class="col xl-8-12">\
				<div class="wrap xl-flexbox xl-middle xl-gutter-8">\
					<div class="col xl-2-12">\
						<picture class="profile-picture big" '+ printPicture +'>\
							<span class="abbr">'+ nameAbbr +'</span>\
						</picture>\
					</div>\
					<div class="col xl-10-12">\
						<span class="full-name">'+ fullName +'</span>\
						<span class="email">('+ email +')</span>\
						<span class="owner-badge">ME</span>\
					</div>\
				</div>\
			</div>\
			<div class="col xl-4-12 text-uppercase dropdown click-to-open access">\
				<a href="#">'+ shareText +' <i class="fa fa-caret-down change-access"></i></a>\
				<ul class="no-delay right selectable change-access">\
					<li class="'+ ( mStatus == "shared" ? "selected" : "" ) +' hide-if-me"><a href="#" data-action="changeshareaccess">THIS '+dataType+'</a></li>\
					<li class="'+ ( mStatus == "project" ? "selected" : "" ) +' hide-if-me hide-when-project" data-action="changeshareaccess"><a href="#">WHOLE PROJECT</a></li>\
					<li class="'+ ( mStatus == "owner" ? "selected" : "" ) +' hide-if-not-owner hide-if-not-confirmed"><a href="#" data-action="makeownerof" data-confirm="Are you sure you want to make this user owner of this '+dataType+'?">'+dataType+' OWNER</a></li>\
					<li><a href="#" data-action="unshare" data-confirm="Are you sure you want to remove access for this user?">REMOVE ACCESS</a></li>\
				</ul>\
			</div>\
		</li>\
	';

}



// HELPERS:
function get_client_cache(key) {


	if (localStorage) {

		return localStorage.getItem(key);

	} else {

		// No support. Use a fallback such as browser cookies or store on the server. !!!
		return false;

	}


}

function set_client_cache(key, value) {


	if (localStorage) {

		localStorage.setItem(key, value);
		return true;

	} else {

		// No support. Use a fallback such as browser cookies or store on the server. !!!
		return false;

	}


}

function remove_client_cache(key) {


	if (localStorage) {

		localStorage.removeItem(key);
		return true;

	} else {

		// No support. Use a fallback such as browser cookies or store on the server. !!!
		return false;

	}


}

function currentUrl() {

	//return window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search;
	return window.location.href;

}

function urlStandardize(url, removeProtocol) {


	removeProtocol = assignDefault(removeProtocol, false);


	// Remove hash
	url = url.split('#')[0];


	// Split from query string
	if ( url.split('?').length === 1 ) {


		// Remove slash at the end
		url = url.replace(/\/$/,'');


	} else if ( url.split('?').length > 1 ) {


		// Remove slash from the end of the string before query
		url = url.split('?')[0].replace(/\/$/,'') + '?' + url.split('?')[1];

	}


	// Remove the protocol
	if (removeProtocol) {

		url = url.replace('http://', '').replace('https://', '');

	}


	return url;
}

function getDomainName(url) {

	var sourceString = url.replace('http://', '').replace('https://', '').replace('www.', '').split(/[/?#]/)[0];

	return sourceString;
}

function removeQueryArgFromCurrentUrl(arg) {


	var value = getParameterByName(arg, currentUrl());
	var change = true;
	if ( arg == "new" && value == "page" ) change = false;


	// If being force reinternalizing, update the URL
	if (history.replaceState && change) {
	    var newurl = queryParameter(currentUrl(), arg, "");
	    if (newurl != currentUrl()) window.history.replaceState({path:newurl},'',newurl);
	}


}

function cleanHTML(s, cleanAll) {


	cleanAll = assignDefault(cleanAll, false);


	// Clean all HTML tags
	if (cleanAll) return s.replace(/(<([^>]+)>)/ig,"");


	// Clean HTML tags, except BRs
	s = s.replace(/<(br)[^>]+>/ig,'<$1>');
	s.replace(/(<(?!br\s*\/?)[^>]+>)/ig,"");


	// BR to new line sign
	s = s.replace(/<br\s*\/?>/gi, '↲<div></div>');


	return s;

}

function getParameterByName(name, url) {

	url = assignDefault(url, window.location.href);
	
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function queryParameter(url, key, value) {


	// Default
	value = assignDefault(value, null);


	var urlParsed = new URL(url);
	var query_string = urlParsed.search;
	var search_params = new URLSearchParams(query_string);


	if (value == "") search_params.delete(key);
	else search_params.set(key, value);


	if (value == null) return getParameterByName(key, url);


	urlParsed.search = search_params.toString();
	var new_url = urlParsed.toString();

	return new_url;
}

function formatBytes(bytes, decimals) {
   if(bytes == 0) return '0 Bytes';
   var k = 1024,
       dm = decimals <= 0 ? 0 : decimals || 2,
       sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
       i = Math.floor(Math.log(bytes) / Math.log(k));
   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function timeSince(date) {

	// UTC
	var now = new Date();
	var seconds = Math.floor((new Date(now.getTime() + now.getTimezoneOffset() * 60000) - date) / 1000);

	var interval = Math.floor(seconds / 31536000);
	if (interval > 1) return interval + " years";
	if (interval == 1) return interval + " year";

	interval = Math.floor(seconds / 2592000);
	if (interval > 1) return interval + " months";
	if (interval == 1) return interval + " month";

	interval = Math.floor(seconds / 86400);
	if (interval > 1) return interval + " days";
	if (interval == 1) return interval + " day";

	interval = Math.floor(seconds / 3600);
	if (interval > 1) return interval + " hours";
	if (interval == 1) return interval + " hour";

	interval = Math.floor(seconds / 60);
	if (interval > 1) return interval + " minutes";
	if (interval == 1) return interval + " minute";

	//return Math.floor(seconds) + " seconds";
	return "about a minute";
}

function getFileExtension(fileName) {

	var re = /(?:\.([^.]+))?$/;
	return re.exec(fileName)[1];

	// var ext = re.exec("file.name.with.dots.txt")[1];   // "txt"
	// var ext = re.exec("file.txt")[1];                  // "txt"
	// var ext = re.exec("file")[1];                      // undefined
	// var ext = re.exec("")[1];                          // undefined
	// var ext = re.exec(null)[1];                        // undefined
	// var ext = re.exec(undefined)[1];                   // undefined

}

function assignDefault(variable, defaultValue) {

	return (typeof variable !== 'undefined') ? variable : defaultValue;

}

function ajax(type, givenData) {


	// Default
	givenData = assignDefault(givenData, {});


	givenData.type = type;

	return $.ajax({
		method: "POST",
		url: ajax_url,
		data: givenData,
		//async: false,
		dataType: 'json',
		timeout: 10000
	});

}

function log(log, arg1) {
	//console.log(log, arg1);
}

function removeDuplicates(array, prop) {
	var arrayMap = array.map( function(el){return el[prop];});
	return array.filter( function(obj, index) {
		return arrayMap.indexOf(obj[prop]) === index;
	});
}

function copyToClipboard(selector) {

	var temp = $("<input>");
	$("body").append(temp);

	temp.val( $(selector).text() ).select();

	document.execCommand("copy");
	temp.remove();

}

function getAbsoluteUrl(url, selectedDocument) {

	selectedDocument = assignDefault(selectedDocument, document);

	var a = selectedDocument.createElement('a');
	a.href = url;

	return a.href;

}

function MD5(s) {
	// MD5 (Message-Digest Algorithm) by WebToolkit
	function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]|(G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k="",F="",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F="0"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,"n");var d="";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()
}

function get_gravatar(email, s = 80, d = 'blank', r = 'g') {

	if (typeof email === "undefined") return null;

    var url = 'https://www.gravatar.com/avatar/';
    url += MD5( email.trim().toLowerCase() );
    url += "?s=" + s + "&d=" + d + "&r=" + r + "";

    return url;
}