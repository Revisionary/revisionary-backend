<?php
use Cocur\BackgroundProcess\BackgroundProcess;


class Internalize {


	// The Phase ID
	public $phase_ID;

	// The Phase data
	public $phaseData;

	// The Project ID
	public $project_ID;

	// The Page ID
	public $page_ID;

	// The Device ID
	public $device_ID;

	// The Device data
	public $deviceData;

	// The Queue ID
	public $queue_ID;



	// HTML file to download
	public $downloadedHTML = array();

	// Serverside rendered file and normal file character difference
	public $renderDifference = 0;

	// CSS files to download
	public $downloadedCSS = array();

	// JS files to download
	public $downloadedJS = array();

	// Fonts to download
	public $downloadedFonts = array();

	// Images to download !!! Not yet
	public $downloadedImages = array();



	// When initialized
	public function __construct($phase_ID, $page_ID, $device_ID, $queue_ID) {


		// Set the phase ID
		$this->phase_ID = $phase_ID;

		// Get the phase data
		$this->phaseData = Phase::ID($phase_ID);


		// Set the project ID
		$this->project_ID = $this->phaseData->getInfo('project_ID');

		// Set the page ID
		$this->page_ID = $page_ID;


		// Set the device ID
		$this->device_ID = $device_ID;

		// Get the device data
		$this->deviceData = Device::ID($device_ID);


		// The current queue ID
		$this->queue_ID = $queue_ID;


	}




	// JOBS:


	// 1. Wait for the queue
	public function waitForQueue() {
		global $db, $queue, $logger;


		// 1.1. Check if current job is ready to be done
		$job_ready = $queue->isReady($this->queue_ID);
		$job_status = $queue->info($this->queue_ID)['queue_status'];


		// 1.2. Wait for the job availability in queue
		$interval = 2;
		while (!$job_ready && $job_status == "waiting") {

			$logger->info("Waiting $interval second(s) for the queue.");
			sleep($interval);
			$job_ready = $queue->isReady($this->queue_ID);
			$job_status = $queue->info($this->queue_ID)['queue_status'];

		}

		return true;
	}



	// 2. 	If job is ready to get done, open the site with Chrome
	// 2.1. Download the HTML, CSS, JS and Font files
	// 2.2. Take a screenshot for the device, and project if not exist
	// 2.3. JSON Output all the downloaded files
	public function browserWorks($page_type = "url") {
		global $db, $queue, $logger, $config;


		// INITIAL LOGS

		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job is starting.");
		$logger->info("Browser job is starting.");


		// Phase Info
		$phase_ID = $this->phase_ID;
		$url = $this->phaseData->remoteUrl;
		$phaseDir = $this->phaseData->phaseDir;


		// Project Info
		$project_ID = $this->project_ID;


		// Page Info
		$page_ID = $this->page_ID;


		// Device Info
		$device_ID = $this->device_ID;
		$deviceInfo = $this->deviceData->getInfo();


		// Screen info
		$screenInfo = Screen::ID($deviceInfo['screen_ID'])->getInfo();


		$width = $deviceInfo['device_width'] ? $deviceInfo['device_width'] : $screenInfo['screen_width'];
		$height = $deviceInfo['device_height'] ? $deviceInfo['device_height'] : $screenInfo['screen_height'];


		// Chrome container request link
		$processLink = $config['env']['chrome_url'] . "/";
		$processLink .= "?url=".urlencode($url);
		$processLink .= "&action=internalize";
		$processLink .= "&width=$width&height=$height";
		$processLink .= "&project_ID=$project_ID";
		$processLink .= "&page_ID=$page_ID";
		$processLink .= "&phase_ID=$phase_ID";
		$processLink .= "&device_ID=$device_ID";
		$processLink .= "&page_type=$page_type";


		$logger->info("Process URL String: $processLink");


		// Send the request
		$data = getRemoteData($processLink);


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job is done.");
		$logger->info("Browser job is done.");


		// If not successful
		if (!$data || $data->status != "success" || count($data->downloadedFiles) == 0) {


			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "Downloaded JS file list is not exist. Trying one more time...");
			$logger->error("Downloaded JS file list is not exist. Trying one more time...");



			// Send the request again after 2 seconds
			sleep(2);
			$data = getRemoteData($processLink);

			if (!$data || $data->status != "success" || count($data->downloadedFiles) == 0) {


				// Update the queue status
				$queue->update_status($this->queue_ID, "error", "Downloaded JS file list is not exist.");
				$logger->error("Downloaded JS file list is not exist.");

				return false;

			}

		}



		// Update the real URL
		if ( isset($data->realPageURL) && $data->realPageURL != $url ) {

			$url = $data->realPageURL;
			Page::ID($page_ID)->edit('page_url', $data->realPageURL);
			$this->phaseData = Phase::ID($phase_ID);

		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Downloaded files list is ready.");
		$logger->info("Downloaded files list is ready: ", $data->downloadedFiles);



		// Parse and detect downloaded files
		foreach($data->downloadedFiles as $file) {

			if (!is_object($file)) {




				/*
				// Update the queue status
				$queue->update_status($this->queue_ID, "error", "Downloaded JS file list data type is wrong.");
				$logger->error("Downloaded JS file list data type is wrong. (".gettype($file).", ".gettype($data->downloadedFiles).", ".print_r($data->downloadedFiles, true).")");

				return false;
				*/


				continue;
			}


			$fileData = array(
				'new_file_name' => $file->newFileName,
				'url'			=> $file->remoteUrl
			);


			if ($file->fileType == "stylesheet") {

				$this->downloadedCSS[] = $fileData;

			} elseif ($file->fileType == "script") {

				$this->downloadedJS[] = $fileData;

			} elseif ($file->fileType == "font") {

				$this->downloadedFonts[] = $fileData;

			}

		}


		// Serverside rendered file and normal file character difference
		$this->renderDifference = $data->renderDifference;


		// Log
		$logger->debug("Downloaded CSS files:", $this->downloadedCSS);
		$logger->debug("Downloaded JS files:", $this->downloadedJS);
		$logger->debug("Downloaded Font files:", $this->downloadedFonts);
		$logger->debug("SSR Difference: ".$this->renderDifference);



		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "Browser job is complete.");
		$logger->info("Browser job is complete.");


		return true;

	}


	// 3. HTML absolute URL filter to correct downloaded URLs
	public function filterAndUpdateHTML($page_type = "url") {
		global $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");

			return false;
		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "HTML Filtering started.");
		$logger->info("HTML Filter started.");


		// Do nothing if there is no HTML file
		if ( !file_exists( $this->phaseData->phaseFile ) ) {

			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "HTML couldn't be filtered. (No file)");
			$logger->error("HTML file is not exist.");

			return false;
		}


		// GET HTML CONTENT
		// Get the HTML from the downloaded file
		$html = file_get_contents($this->phaseData->phaseFile);



		// Specific Log
		file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Started \r\n", FILE_APPEND);



		// Add Necessary Spaces - done for a bug
		function placeNeccessarySpaces($contents) {
			$quotes = 0; $flag = false;
			$newContents = '';
			for($i = 0; $i < strlen($contents); $i++){
			    $newContents .= $contents[$i];
			    if(is_array($contents) && $contents[$i] == '"') $quotes++;
			    if($quotes%2 == 0){
			        if(is_array($contents) && $contents[$i+1] !== ' ' && $flag == true) {
			            $newContents .= ' ';
			            $flag = false;
			        }
			    }
			    else $flag = true;
			}
			return $newContents;
		}
		$html = placeNeccessarySpaces($html);



		// INCLUDE THE BASE AND HELPER SCRIPT
		$countHead = 0;
		$html = preg_replace_callback(
	        '/<head([\>]|[\s][^<]*?\>)/i',
	        function ($urls) {
		        global $countHead;
		        $countHead++;
		        $head_tag = $urls[0];
		        if ( $countHead == 1 ) {
			        // Specific Log
					file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Base Added: '".$this->phaseData->remoteUrl."' \r\n", FILE_APPEND);
					$new_base = "<base href='".$this->phaseData->remoteUrl."'>";
					$new_base .= "<script src='".asset_url('scripts/internalized.js', true)."'></script>";
			        return $head_tag.$new_base;
		        }
		        return $head_tag;
	        },
	        $html
	    );




		// If no <head> tag, add it after the <html> tag
	    if ($countHead == 0 && $page_type != "ssr") {

			$countHtml = 0;
			$html = preg_replace_callback(
		        '/<html([\>]|[\s][^<]*?\>)/i',
		        function ($urls) {
			        global $countHtml;
			        $countHtml++;

			        $html_tag = $urls[0];

			        if ( $countHtml == 1 ) {

				        // Specific Log
						file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Base Added: '".$this->phaseData->remoteUrl."' \r\n", FILE_APPEND);

						$new_base = "<base href='".$this->phaseData->remoteUrl."'>";
						$new_base .= "<script src='".asset_url('scripts/internalized.js', true)."'></script>";

						return $html_tag.$new_base;

			        }

			        return $html_tag;

		        },
		        $html
		    );

	    }


		// INTERNALIZE CSS FILES
		$html = preg_replace_callback(
	        '/<link[\s]+[^<>]*[\s]*href=(?:(?:["](?<value>[^<>\'"\s]+)["])|(?:[\'](?<value2>[^<>\'"\s]*)[\'])|(?:[^\'"]*?(?<value3>[^<>\'"\s]*)[^\'"]*?)).*?[>$]/is',
	        function ($urls) {


		        // Found parts
				$full_tag = $new_full_tag = $urls[0];
				$the_url = isset($urls['value3']) ? $urls['value3'] : (isset($urls['value2']) ? $urls['value2'] : $urls['value']);


		        // Remove extra slashes from the URL
		        $the_url_clean = str_replace('\/', '/', $the_url);


		        // Absoluted URL
		        $new_url = url_to_absolute($this->phaseData->remoteUrl, $the_url_clean);


				// If it has host, but no protocol (without http or https)
		        if (parseUrl($the_url_clean)['host'] != "" )
		        	$new_url = url_to_absolute(parseUrl($the_url_clean)['full_host'], $the_url_clean);


		        // If not on our server, don't touch it !!!
		        if (parseUrl($the_url_clean)['domain'] != "" && parseUrl($the_url_clean)['domain'] != parseUrl($this->phaseData->remoteUrl)['domain'] )
		        	$new_url = $the_url_clean;



				// Find in downloads
		        $css_resource_key = array_search($new_url, array_column($this->downloadedCSS, 'url'));

				// Check without hash
				if ($css_resource_key === false)
					$css_resource_key = array_search(parseUrl($new_url)['full_path'], array_column($this->downloadedCSS, 'url'));


		        // If file is from the remote url, and already downloaded
		        if (
		        	//parseUrl($new_url)['domain'] == parseUrl($this->phaseData->remoteUrl)['domain'] && // TO ALLOW CDN URLs !!!
		        	$css_resource_key !== false &&
		        	(	// Check if this is really stylesheet calling tag
		        		strpos($full_tag, 'rel="stylesheet"') !== false ||
		        		strpos($full_tag, "rel='stylesheet'") !== false ||
		        		strpos($full_tag, "rel=stylesheet") !== false
		        	)

		        ) {


			        // Downloaded file name
					$css_file_name = $this->downloadedCSS[$css_resource_key]['new_file_name'];


			        // Change the URL again with the downloaded file
			        $new_url = $this->phaseData->phaseUri."css/".$css_file_name;



			        // Specific Log
					file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - CSS - Internalized URLs: '".$the_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				}



		        // Update the HTML element
	            $new_full_tag = str_replace(
	            	"href='$the_url", // with single quote
	            	"href='$new_url",
	            	$full_tag
	            );

	            $new_full_tag = str_replace(
	            	"href=\"$the_url", // with double quotes
	            	"href=\"$new_url",
	            	$new_full_tag
	            );

	            $new_full_tag = str_replace(
	            	"href=$the_url", // without quotes
	            	"href=$new_url",
	            	$new_full_tag
	            );



		        // Specific Log
				file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - CSS - Absoluted URLs: '".$the_url."' -> '".$new_url."' \r\n", FILE_APPEND);
				file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - CSS - Absoluted HTML: '".$full_tag."' -> '".$new_full_tag."' \r\n", FILE_APPEND);


	            return $new_full_tag;
	        },
	        $html
	    );


		// INTERNALIZE JS FILES
		if ($page_type != "ssr") {

			$html = preg_replace_callback(
		        '/<script[\s]+[^<>]*[\s]*src=(?:(?:["](?<value>[^<>\'"\s]+)["])|(?:[\'](?<value2>[^<>\'"\s]*)[\'])|(?:[^\'"]*?(?<value3>[^<>\'"\s]*)[^\'"]*?)).*?[>$]/is',
		        function ($urls) {


			        // Found parts
					$full_tag = $new_full_tag = $urls[0];
					$the_url = isset($urls['value3']) ? $urls['value3'] : (isset($urls['value2']) ? $urls['value2'] : $urls['value']);


			        // Remove extra slashes from the URL
			        $the_url_clean = str_replace('\/', '/', $the_url);


			        // Absoluted URL
			        $new_url = url_to_absolute($this->phaseData->remoteUrl, $the_url_clean);


					// If it has host, but no protocol (without http or https)
			        if (parseUrl($the_url_clean)['host'] != "" )
			        	$new_url = url_to_absolute(parseUrl($the_url_clean)['full_host'], $the_url_clean);


			        // If not on our server, don't touch it !!!
			        if (parseUrl($the_url_clean)['domain'] != "" && parseUrl($the_url_clean)['domain'] != parseUrl($this->phaseData->remoteUrl)['domain'] )
			        	$new_url = $the_url_clean;



					// Find in downloads
			        $js_resource_key = array_search($new_url, array_column($this->downloadedJS, 'url'));

					// Check without hash
					if ($js_resource_key === false)
						$js_resource_key = array_search(parseUrl($new_url)['full_path'], array_column($this->downloadedJS, 'url'));


			        // If file is from the remote url, and already downloaded
			        if ($js_resource_key !== false) {

				        // Downloaded file name
						$js_file_name = $this->downloadedJS[$js_resource_key]['new_file_name'];


				        // Change the URL again with the downloaded file
				        $new_url = $this->phaseData->phaseUri."js/".$js_file_name;



				        // Specific Log
						file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - JS - Internalized URLs: '".$the_url."' -> '".$new_url."' \r\n", FILE_APPEND);

					}



			        // Update the HTML element
		            $new_full_tag = str_replace(
		            	"src='$the_url", // with single quote
		            	"src='$new_url",
		            	$full_tag
		            );

		            $new_full_tag = str_replace(
		            	"src=\"$the_url", // with double quotes
		            	"src=\"$new_url",
		            	$new_full_tag
		            );

		            $new_full_tag = str_replace(
		            	"src=$the_url", // without quotes
		            	"src=$new_url",
		            	$new_full_tag
		            );




			        // Specific Log
					file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - JS - Absoluted URLs: '".$the_url."' -> '".$new_url."' \r\n", FILE_APPEND);
					file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - JS - Absoluted HTML: '".$full_tag."' -> '".$new_full_tag."' \r\n", FILE_APPEND);



		            return $new_full_tag;
		        },
		        $html
		    );

	    }



	    // FILTER IN PAGE STYLES
		$html = preg_replace_callback(
	        '/(?<tag><style+[^<]*?>)(?<content>[^<>]++)<\/style>/i',
	        function ($style) {

		        // Specific Log
				file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Inpage Style Filtred \r\n", FILE_APPEND);

		        return $style['tag'].$this->filter_css($style['content'])."</style>";

	        },
	        $html
	    );


	    // FILTER INLINE STYLES
		$html = preg_replace_callback(
	        '/<(?:[a-z0-9]*)\s+[^<]*?(?:style)=(?:(?:[\"](?<value>[^<]*?)[\"])|(?:[\'](?<value2>[^<]*?)[\'])).*?>/i',
	        function ($urls) {

		        $the_css = isset($urls['value2']) ? $urls['value2'] : $urls['value'];
		        $filtred_css = $this->filter_css($the_css);

		        // Specific Log
				file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Inline Style Filtred: '".$the_css."' -> '".$filtred_css."' \r\n", FILE_APPEND);


	            return str_replace(
	            	$the_css,
	            	$filtred_css,
	            	$urls[0]
	            );
	        },
	        $html
	    );


	    // PUT THE ELEMENT INDEXES
	    $countElement = 0;
	    $html = str_replace('data-revisionary-index=', 'rvi=', $html);
	    $html = str_replace('<body ', '<body data-revisionary-index=0 ', $html);
	    $html = str_replace('<body>', '<body data-revisionary-index=0>', $html);
		$html = preg_replace_callback(
	        '/<body[^<]*?>|(?!^)\G(.*?)(?<tag><(?<tagname>[a-z1-9]+[1-9]?)\s?[^<]*?>)/si',
	        function ($matches) {
		        global $countElement;
		        $html_element = $matches[0];


				// If it couldn't be found correctly
		        if (
		        	!isset($matches['tag']) ||
		        	!isset($matches['tagname'])
		        ) return $html_element;


		        $tag 	  = $matches['tag'];
		        $tag_name = $matches['tagname'];


		        $countElement++;


				// If ends with '/>'...
				if ( substr($tag, -2 ) == '/>' ) $new_tag = str_replace('/>', ' data-revisionary-index='.$countElement.' />', $tag);

				// If ends with only '>'...
				elseif ( substr($tag, -1 ) == '>' ) $new_tag = str_replace('>', ' data-revisionary-index='.$countElement.' >', $tag);


				// Specific Log
				file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - ELEMENT TO INDEX (#$countElement): \r\n
				".$tag." \r\n
				".$new_tag."' \r\n \r\n", FILE_APPEND);


		        $new_html_element = str_replace($tag, $new_tag, $html_element);


		        return $new_html_element;
	        },
	        $html
		);



		// SAVING:

		// Save the file if exists
		if ( file_exists( $this->phaseData->phaseFile ) )
			$updated = file_put_contents( $this->phaseData->phaseFile, $html, FILE_TEXT);


		// Specific Log
		file_put_contents( $this->phaseData->logDir."/_html-filter.log", "[".date("Y-m-d h:i:sa")."] - Finished".(!$updated ? " <b>WITH ERRORS</b>":'')." \r\n", FILE_APPEND);
		rename($this->phaseData->logDir."/_html-filter.log", $this->phaseData->logDir.(!$updated ? '/__' : '/')."html-filter.log");


		if (!$updated) {

			// Update the queue status
			$queue->update_status($this->queue_ID, "error", "HTML couldn't be filtred.");
			$logger->error("HTML couldn't be filtred.");

			return false;
		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "HTML Filtred.");
		$logger->info("HTML Filtred.");


		return true;
	}


	// 4. Filter CSS files
	// 4.1. Absolute URL filter to correct downloaded URLs
	// 4.2. Detect fonts and correct with downloaded ones
	public function filterAndUpdateCSSfiles() {
		global $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");
			return false;

		}



		// Update the queue status
		$queue->update_status($this->queue_ID, "working", "CSS filtering started.");

		// Init Log
		$logger->info("CSS filtering started.");

		// Init Log
		$logger->debug("Total CSS: ". count($this->downloadedCSS), $this->downloadedCSS);



		// Specific Log
		file_put_contents( $this->phaseData->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Started {TOTAL:".count($this->downloadedCSS)."} \r\n", FILE_APPEND);




		$count = 0;
		$css_filtered_has_error = false;


		// Do nothing if there is no CSS file
		if ( count($this->downloadedCSS) > 0 ) {


			// Filter them
			foreach ($this->downloadedCSS as $info) {

				$fileName = $info['new_file_name'];
				$css_url = $info['url'];
				$fileUri = $this->phaseData->phaseDir."/css/".$fileName;


				// Get the old CSS
				$old_css = file_get_contents($fileUri);


				// Filter
				$filteredCSS = $this->filter_css($old_css, $css_url);


				// Check if empty
				if ( empty($filteredCSS) ) $filteredCSS = "/* File is empty */";


				// Save the new CSS
				$css_filtered = file_put_contents($fileUri, $filteredCSS);


				// Specific Log
				file_put_contents( $this->phaseData->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] -".(!$css_filtered ? " <b>NOT</b>":'')." Filtered: '".$css_url."' -> '".$fileName."' \r\n", FILE_APPEND);

				if (!$css_filtered) $css_filtered_has_error = true;

				$count++;
			}


		}



		// Specific Log
		file_put_contents( $this->phaseData->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Finished".($css_filtered_has_error ? " <b>WITH ERRORS</b>":'')." \r\n", FILE_APPEND);
		rename($this->phaseData->logDir."/_css-filter.log", $this->phaseData->logDir."/".($css_filtered_has_error ? "__" : "")."css-filter.log");


		// Return true if no error
		if (!$css_filtered_has_error) {

			// Update the queue status
			$queue->update_status($this->queue_ID, "working", "$count CSS filtering finished.");

			$logger->info("$count CSS filtering finished");
			return true;
		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "error", "$count CSS filtering finished with error(s).");


		$logger->error("$count CSS filtering finished with error(s).");
		return false;

	}


	// 5. Complete the job!
	public function completeTheJob() {
		global $logger, $queue;


		// Current Queue Status Check
		if ( $queue->info($this->queue_ID)['queue_status'] != "working" ) {

			$logger->error("Queue isn't working.");
			return false;

		}


		// Update the queue status
		$queue->update_status($this->queue_ID, "done", "Internalization is complete.");


		// Increase the internalization count
		$newInternalizeCount = $this->phaseData->internalizeCount + 1;
		$this->phaseData->edit('phase_internalized', $newInternalizeCount);


		// Init Log
		$logger->info("Internalization #$newInternalizeCount is complete.");


		return $this->phaseData->cachedUrl;
	}



	// FILTERS:

	// FILTER CSS
	function filter_css($css, $url = "") {
		global $logger;


		if (empty($url))
			$url = $this->phaseData->remoteUrl;


		// Internalize Fonts - No Need for now !!!
		//$css = $this->detectFonts($css, $url);


		// Log
		$logger->info('CSS filtering started: '.$url);


		// All url()s
		$count = 0;
		$css = preg_replace_callback(
	        '/url\s*\(\s*[\\\'"]?(?<url>[^\\\'")]+)[\\\'"]?\s*\)/',
	        function ($css_urls) use($url) {
        		global $logger, $count;

				$url_found = $css_urls['url'];

        		$relative_url = $url_found;
        		$absolute_url = url_to_absolute($url, $url_found);
				$new_url = $absolute_url;


        		// Absolution Logs
				$logger->debug('Full URL absoluted in CSS: ', $css_urls);
				$logger->info('URL absoluted in CSS: '.$relative_url.' -> '.$new_url);
		        file_put_contents( $this->phaseData->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Absoluted: '".$relative_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				$parsed_url = parseUrl($absolute_url);
				$parsed_path = pathinfo($parsed_url['path']);
		        $file_name = $parsed_path['filename'];
		        $file_hash = isset($parsed_url['hash']) ? $parsed_url['hash'] : "";
		        $file_extension_with_hash = isset($parsed_path['extension']) ? $parsed_path['extension'] : "";
		        $file_extension = str_replace('#'.$file_hash, '', $file_extension_with_hash);
		        $file_name_hashed = $file_name.".".$file_extension_with_hash;


				// If not valid URL
				if (
					$file_name == "" ||
					$file_extension == "" ||
					substr($url_found, 0, 1) == "#" // On id clip-pathes
				) {

					$logger->info('Invalid URL skipped in CSS: '.$url_found);

					return "url(".$url_found.")";
				}


				/* // REMOVED TO ALLOW CDN URLs !!!
				// If not same domain URL
				if (
					$parsed_url['domain'] != parseUrl($this->phaseData->remoteUrl)['domain']
				) {

					$logger->info('Different domain URL skipped in CSS: '.$absolute_url);

					return "url('".$url_found."')";
				}
				*/



		        // Find CSS in downloads
		        $css_resource_key = array_search($absolute_url, array_column($this->downloadedCSS, 'url'));

		        // Check without hash
				if ($css_resource_key === false)
					$css_resource_key = array_search(parseUrl($absolute_url)['full_path'], array_column($this->downloadedCSS, 'url'));


		        // Find Font in downloads
		        $font_resource_key = array_search($absolute_url, array_column($this->downloadedFonts, 'url'));

				// Check without hash
				if ($font_resource_key === false)
					$font_resource_key = array_search(parseUrl($absolute_url)['full_path'], array_column($this->downloadedFonts, 'url'));


				if (
					$font_resource_key !== false &&
					(
						$file_extension == "ttf" ||
						$file_extension == "otf" ||
						$file_extension == "woff" ||
						$file_extension == "woff2" ||
						$file_extension == "svg" ||
						$file_extension == "eot"
					)
				) {

					// Downloaded file name
					$font_file_name = $this->downloadedFonts[$font_resource_key]['new_file_name'];

					$new_url = $this->phaseData->phaseUri."fonts/".$file_name_hashed;


					// Font Logs
					$logger->info('Imported Font Detected: '.$relative_url.' -> '.$new_url);
			        file_put_contents( $this->phaseData->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Imported Font Detected: '".$relative_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				} elseif ( $css_resource_key !== false ) {

					// Downloaded file name
					$downloaded_css = $this->downloadedCSS[$css_resource_key]['new_file_name'];

					$new_url = $this->phaseData->phaseUri."css/".$downloaded_css;


					// CSS Import Logs
					$logger->info('Imported CSS Detected: '.$relative_url.' -> '.$new_url);
			        file_put_contents( $this->phaseData->logDir."/_css-filter.log", "[".date("Y-m-d h:i:sa")."] - Imported CSS Detected: '".$relative_url."' -> '".$new_url."' \r\n", FILE_APPEND);


				}

				$count++;

	            return "url(".$new_url.")";
	        },
	        $css
	    );



		// Log
		$logger->info('CSS filtering finished: '.$url);


		return $css;

	}


	// FILTER JS - NOT YET !!!
	function filter_js() {

	}

}
