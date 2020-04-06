<?php

// SITE URLS

function url($index) {
  global $_url;
  if (isset($_url[$index]))
    return $_url[$index];
  return false;
}

function urlStandardize($url) {

	// Remove hash
	$url = strtok($url, "#");

	return $url;
}

function site_url($url = null, $forceSSL = false, $unForceSSL = false) {
  return ($forceSSL ? secure_url : ($unForceSSL ? insecure_url : url)) . '/' . $url;
}

function asset_url($url = null, $forceSSL = false, $unForceSSL = false) {
  return ($forceSSL ? secure_url : ($unForceSSL ? insecure_url : url)) . '/assets/' . $url;
}

function asset_url_nocache($url = null) {

	$filemtime = filemtime(dir.'/assets/'.$url);

	$full_url = asset_url($url);
	$new_url = queryArg("v=$filemtime", $full_url);

	return $new_url;
}

function cache_url($url = null, $forceSSL = false, $unForceSSL = false) {
  return ($forceSSL ? secure_url : ($unForceSSL ? insecure_url : url)) . '/cache/' . $url;
}

function current_url($query = "", $removeQuery = "", $forceSSL = false, $unForceSSL = false) {


	// Get current host
	$pageURL = 'http'.(ssl ? "s" : "")."://".$_SERVER["SERVER_NAME"];


	// SSL forcing
	if ($forceSSL) $pageURL = secure_url;
	if ($unForceSSL) $pageURL = insecure_url;


	// Port detection
	$pageURL .= ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ? ":".$_SERVER["SERVER_PORT"] : "").$_SERVER["REQUEST_URI"];



	// Query works
	if ($query != "")
		$pageURL = queryArg($query, $pageURL);

	if ($removeQuery != "")
		$pageURL = removeQueryArg($removeQuery, $pageURL);


	return $pageURL;

}

function permalink($str, $options = array()) {
    $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => true
    );
    $options = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
        'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
        'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
        'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
        'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
        'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
        'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
        'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
        // Turkish
        'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
        'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
        // Russian
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
        'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
        'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
        // Czech
        'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
        'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
        'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
        'š' => 's', 'ū' => 'u', 'ž' => 'z'
    );
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . ') {2,}/', '$1', $str);
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}


// URL PARSER
function parseUrl($url) {


	// Sanitize Quotes
	$url = str_replace("'", "", $url);
	$url = str_replace('"', '', $url);


	if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) return false;



	/*
	http://user:pass@www.pref.okinawa.jp:8080/path/to/page.html?query=string#fragment

	Array
	(
	    [scheme] => http
	    [host] => www.pref.okinawa.jp
	    [port] => 8080
	    [user] => user
	    [pass] => pass
	    [path] => /path/to/page.html
	    [query] => query=string
	    [fragment] => fragment
	)

	*/
	$parsed_url = parse_url($url);

	$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : "";
	$host     = isset($parsed_url['host']) ? $parsed_url['host'] : "";
	$port     = isset($parsed_url['port']) ? $parsed_url['port'] : "";
	$user     = isset($parsed_url['user']) ? $parsed_url['user'] : "";
	$pass     = isset($parsed_url['pass']) ? $parsed_url['pass'] : "";
	$path     = isset($parsed_url['path']) ? $parsed_url['path'] : "";
	$query    = isset($parsed_url['query']) ? $parsed_url['query'] : "";
	$fragment = isset($parsed_url['fragment']) ? $parsed_url['fragment'] : "";

	$full_host = "$scheme://$host";
	$full_path = "$scheme://$host".$path.$query;




	/*
	www.pref.okinawa.jp

	Pdp\Domain Object
	(
	    [domain] => www.pref.okinawa.jp
	    [registrableDomain] => pref.okinawa.jp
	    [subDomain] => www
	    [publicSuffix] => okinawa.jp
	    [isKnown] => 1
	    [isICANN] => 1
	    [isPrivate] =>
	)

	*/
	$manager = new Pdp\Manager(new Pdp\Cache(), new Pdp\CurlHttpClient());
	$rules = $manager->getRules(); //$rules is a Pdp\Rules object
	$parsed_domain = $rules->resolve($host); //$domain is a Pdp\Domain object




	if ( isset($parsed_url['path']) && strpos(basename($parsed_url['path']), '.') !== false )
		$full_path = str_replace(basename($full_path), '', $full_path);



	return array(
		'scheme' 	 => $scheme,
		'host' 		 => $host,
		'full_host' => $full_host,
		'path' 		 => $path,
		'full_path' => $full_path,
		'hash' 		 => $fragment,

		'domain' 	 => $parsed_domain->getRegistrableDomain(),
		'subdomain' => $parsed_domain->getSubDomain()
	);

}


// CONVERT RELATIVE URLS TO ABSOULUTE
// https://stackoverflow.com/a/4444490
function url_to_absolute($base, $rel) { //error_log("BASE: $base ---- RELATIVE: $rel");
    /* return if already absolute URL */
    if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

    /* queries and anchors */
    if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

    /* parse base URL and convert to local variables:
       $scheme, $host, $path */
    extract(parse_url($base));

    /* remove non-directory element from path */
    $path = preg_replace('#/[^/]*$#', '', $path);

    /* destroy path if relative url points to root */
    if ($rel[0] == '/') $path = '';

    /* dirty absolute URL */
    $abs = "$host$path/$rel";

    /* replace '//' or '/./' or '/foo/../' with '/' */
    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
    for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

    /* absolute URL is ready! */
    return $scheme.'://'.$abs;
}


// QUERY ARGUMENTS
// Add/Update Query Argument ( Usage: queryArg('key=value', $url) )
function queryArg($newQuery, $url) {

	// Parse the URL
	$parsed = parse_url($url);

	// Parse the new query
	parse_str($newQuery, $newParams);


	if ( !isset($parsed['query']) )
		$parsed['query'] = "";


	// Parse the query
	parse_str($parsed['query'], $params);


	// Add
	foreach ($newParams as $key => $value) {
		$params[$key] = $value;
	}

	// Build new query
	$newQuery = http_build_query($params);


	// The URL
	$pageURL = $parsed['scheme']."://".$parsed['host'].$parsed['path']."?".$newQuery;


	// Clean
	$pageURL = trim($pageURL, '=');
	$pageURL = str_replace('=&', '&', $pageURL);


	return $pageURL;

}


// Remove Query Argument ( Usage: removeQueryArg('key', $url) )
function removeQueryArg($key, $url) {

	$parsed = parse_url($url);

	if ( !isset($parsed['query']) )
		return $url;


	// Parse the query
	parse_str($parsed['query'], $params);


	// Delete
	unset($params[$key]);


	// Build new query
	$newQuery = http_build_query($params);


	// The URL
	$pageURL = $parsed['scheme']."://".$parsed['host'].$parsed['path'].(empty($newQuery) ? "" : "?").$newQuery;


	// Clean
	$pageURL = trim($pageURL, '=');
	$pageURL = str_replace('=&', '&', $pageURL);


	return $pageURL;

}


// FOLLOW A SINGLE REDIRECT: (https://gist.github.com/davejamesmiller/dbefa0ff167cc5c08d6d)
// This makes a single request and reads the "Location" header to determine the
// destination. It doesn't check if that location is valid or not.
function get_redirect_target($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = curl_exec($ch);
    curl_close($ch);
    // Check if there's a Location: header (redirect)
    if (preg_match('/^Location: (.+)$/im', $headers, $matches))
        return trim($matches[1]);
    // If not, there was no redirect so return the original URL
    // (Alternatively change this to return false)
    return $url;
}


// FOLLOW ALL REDIRECTS:
// This makes multiple requests, following each redirect until it reaches the
// final destination.
function get_redirect_final_target($url) {


    $cookie = tmpfile();
    $userAgent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31' ; // Google Chrome

    $ch = curl_init($url);

    $options = array(
        CURLOPT_NOBODY => true,
        CURLOPT_CONNECTTIMEOUT => 20, 
        CURLOPT_USERAGENT => $userAgent,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0
    );

    curl_setopt_array($ch, $options);

    $kl = curl_exec($ch);
    $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    
    if ($target) return $target;
    return false;


    // $ch = curl_init($url);
    // curl_setopt($ch, CURLOPT_NOBODY, 1);
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // follow redirects
    // curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // set referer on redirect


    // $agents = array(
    //     'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
    //     'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.9) Gecko/20100508 SeaMonkey/2.0.4',
    //     'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
    //     'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
    //     'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2'
     
    // );
    // curl_setopt($ch, CURLOPT_USERAGENT, $agents[array_rand($agents)]);


    // curl_exec($ch);
    // $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    // curl_close($ch);
    // if ($target)
    //     return $target;
    // return false;

}