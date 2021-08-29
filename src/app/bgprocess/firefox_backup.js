var webpage = require('webpage').create(),
	system = require('system'),
	fs = require('fs'),
	url, width, height, page_image, output_html, resource_done = false;


// Get arguments
url = system.args[1];
width = system.args[2];
height = system.args[3];
page_image = system.args[4];
project_image = system.args[5];
log_dir = system.args[6];



// Viewport size
webpage.viewportSize = { width: width, height: height };


// Open the site
webpage.open(url, function(status){


    // When completely loaded
    webpage.onLoadFinished = function(status) {


		if (status == "success") {

			// All the resources are written
			fs.write(log_dir + '/resources.log', 'DONE \r\n', 'a');
			resource_done = true;



			var date = new Date();
			var logDate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();

			// Output the page screenshot
			if (page_image != "done") {
				webpage.render(page_image, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING
				console.log("["+logDate+"] - Page is captured: '" + page_image + "'");
			}

			// Output the project screenshot
			if (project_image != "done") {
				webpage.render(project_image, { onlyViewport: true }); // !!! CHANGE THIS WHEN IMAGE MODE OF REVISING
				console.log("["+logDate+"] - Project is captured: '" + project_image + "'");
			}

			// Output the HTML
			//if (system.args[5].length === 1) fs.write(output_html, webpage.content, 'w'); // !!! SEPARATE THIS AND CAPTURING




			// Four seconds later
			window.setTimeout(function () {


				webpage.close();
				slimer.exit();


			}, 4000);


		} else {
			console.log("["+logDate+"] - Sorry, the page is not loaded");

			webpage.close();
			slimer.exit();
		}

    };


    // Received resources
	webpage.onResourceReceived = function(response) {
	    //console.log(JSON.stringify(response));
	    if (!resource_done) fs.write(log_dir + '/resources.log', response.contentType + ' -> ' + response.url + '\r\n', 'a');
	};

});




/*

webpage.onResourceReceived = function(response) {
    if (response.stage !== "end") return;
    console.log('Response (#' + response.id + ', stage "' + response.stage + '"): ' + response.url);
};
webpage.onResourceRequested = function(requestData, networkRequest) {
    console.log('Request (#' + requestData.id + '): ' + requestData.url);
};
webpage.onUrlChanged = function(targetUrl) {
    console.log('New URL: ' + targetUrl);
};
webpage.onLoadFinished = function(status) {
    console.log('Load Finished: ' + status);
};
webpage.onLoadStarted = function() {
    console.log('Load Started');
};
webpage.onNavigationRequested = function(url, type, willNavigate, main) {
    console.log('Trying to navigate to: ' + url);
};
*/