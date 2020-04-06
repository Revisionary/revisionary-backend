<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Validation
if ( !is_numeric(request('pin_ID')) || !is_array(request('css')) ) return;


// Get the pin info
$pin_ID = intval(request('pin_ID'));
$css = request('css');



// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?



// Prepare the CSS code to save
$css_code = "";
foreach ($css as $key => $value) {


	// Key checks
	if (
		$key != "display"
		&& $key != "opacity"
		&& $key != "text-align"
		&& $key != "text-decoration-line"
		&& $key != "font-weight"
		&& $key != "font-style"
		&& $key != "color" // !!! Security check
		&& $key != "background-color" // !!! Security check
		&& $key != "background-position-x" // !!! Security check
		&& $key != "background-position-y" // !!! Security check
		&& $key != "background-image"
		&& $key != "background-repeat"
		&& $key != "background-size"
		&& $key != "top"
		&& $key != "right"
		&& $key != "bottom"
		&& $key != "left"
		&& $key != "margin-top"
		&& $key != "margin-right"
		&& $key != "margin-bottom"
		&& $key != "margin-left"
		&& $key != "border-color" // !!! Security check
		&& $key != "border-style" // !!! Security check
		&& $key != "border-top-width" // !!! Security check
		&& $key != "border-right-width" // !!! Security check
		&& $key != "border-bottom-width" // !!! Security check
		&& $key != "border-left-width"
		&& $key != "border-top-left-radius" // !!! Security check
		&& $key != "border-top-right-radius" // !!! Security check
		&& $key != "border-bottom-left-radius" // !!! Security check
		&& $key != "border-bottom-right-radius" // !!! Security check
		&& $key != "padding-top"
		&& $key != "padding-right"
		&& $key != "padding-bottom"
		&& $key != "padding-left"
		&& $key != "width"
		&& $key != "height"
		&& $key != "font-size"
		&& $key != "line-height"
	) return;


	// Value checks
	if (

		// Display rules
		($key == "display" && !is_string($value))

		// Opacity rules
		|| ($key == "opacity" && (!is_numeric($value) || $value > 1 || $value < 0) )

		// Text Align rules
		|| ($key == "text-align" && !is_string($value))

		// Text Decoration rules
		|| ($key == "text-decoration-line" && !is_string($value))

		// Font Weight rules ??!!!
		|| ($key == "font-weight"
			&& $value != "100"
			&& $value != "200"
			&& $value != "300"
			&& $value != "400"
			&& $value != "500"
			&& $value != "600"
			&& $value != "700"
			&& $value != "800"
			&& $value != "900"
			&& $value != "bold"
			&& $value != "bolder"
			&& $value != "light"
			&& $value != "lighter"
			&& $value != "normal"
			&& $value != "unset"
			&& $value != "inherit"
			&& $value != "initial"
		)

		// Font rules !!!
		|| ($key == "font-style" && !is_string($value))
		|| ($key == "font-size" && !is_string($value))
		|| ($key == "line-height" && !is_string($value))

		// Color rules
		|| ($key == "color" && !is_string($value))

		// BG Color rules
		|| ($key == "background-color" && !is_string($value))

		// BG Image rules
		|| ($key == "background-image" && !is_string($value))

		// BG Repeat rules
		|| ($key == "background-repeat" && !is_string($value))

		// BG Size rules
		|| ($key == "background-size" && !is_string($value))

		// Position rules
		|| ($key == "top" && !is_string($value))
		|| ($key == "right" && !is_string($value))
		|| ($key == "bottom" && !is_string($value))
		|| ($key == "left" && !is_string($value))

		// Margin rules
		|| ($key == "margin-top" && !is_string($value))
		|| ($key == "margin-right" && !is_string($value))
		|| ($key == "margin-bottom" && !is_string($value))
		|| ($key == "margin-left" && !is_string($value))

		// Border rules
		|| ($key == "border-top-width" && !is_string($value))
		|| ($key == "border-right-width" && !is_string($value))
		|| ($key == "border-bottom-width" && !is_string($value))
		|| ($key == "border-left-width" && !is_string($value))

		// Padding rules
		|| ($key == "padding-top" && !is_string($value))
		|| ($key == "padding-right" && !is_string($value))
		|| ($key == "padding-bottom" && !is_string($value))
		|| ($key == "padding-left" && !is_string($value))

		// Size rules
		|| ($key == "width" && !is_string($value))
		|| ($key == "height" && !is_string($value))

	) return;


	// Skip other values
	if (

		// Display rules
		($key == "display" && $value != "none")

		// Text Align rules
		|| ($key == "text-align" && $value != "left" && $value != "center" && $value != "justify" && $value != "right")

		// Text Decoration rules
		|| ($key == "text-decoration-line" && $value != "underline" && $value != "none")

		// Font Weight rules
		|| ($key == "font-weight" && $value != "bold" && $value != "normal")

		// Font style rules
		|| ($key == "font-style" && $value != "italic" && $value != "normal")

		// Color rules
		//|| ($key == "color" && !preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $value))

		// BG Color rules
		//|| ($key == "background-color" && !preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $value))

		// BG Image rules
		|| ($key == "background-image" && (!filter_var($value, FILTER_VALIDATE_URL) && $value != "none" ) )

		// BG Repeat rules
		|| ($key == "background-repeat" && $value != "no-repeat" && $value != "repeat-x" && $value != "repeat-y" && $value != "repeat")

		// BG Repeat rules
		|| ($key == "background-size" && $value != "auto" && $value != "cover" && $value != "contain")

	) continue;


	// BG Image Exception
	if ($key == "background-image" && strpos($value, '//') !== false ) $value = "url($value)";


	// Add the code
	$css_code .= "$key:$value !important; ";


}


// If the code output is empty
if (empty($css_code)) $css_code = null;



$pinData = Pin::ID($pin_ID);
if (!$pinData) return;



// Update the pin
$pin_updated = $pinData->updateCSS($css_code);

if ($pin_updated) $status = "Pin Updated: $pin_ID";
else $status = "error";



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'css' => print_r($css, true),
	'css_code' => $css_code,
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID

);

die(json_encode(array(
  'data' => $data
)));
