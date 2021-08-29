<pre>
<?php


$projects = User::ID()->getProjects_v2();
print_r($projects);
die();


use \Firebase\JWT\JWT;

$payload = array(
    // "iss" => "http://example.org",
    // "aud" => "http://example.com",
    // "iat" => time(),
    // "nbf" => time(),
    // "exp" => time() + 2
    "iat" => 1356999524,
    "nbf" => 1357000000,
    "data" => array(
        "test" => "value"
    )
);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
$jwt = JWT::encode($payload, $config['env']['jwt_secret_key']);
echo "JWT TOKEN: <br>";
var_dump($jwt);
echo "<br><br>";

$decoded = JWT::decode($jwt, $config['env']['jwt_secret_key'], array('HS256'));
echo "DECODED JWT TOKEN: <br>";
print_r($decoded);
echo "<br><br>";

/*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

// $decoded_array = (array) $decoded;
// echo "DECODED ARRAY: <br>";
// var_dump($decoded);
// echo "<br><br>";

/**
 * You can add a leeway to account for when there is a clock skew times between
 * the signing and verifying servers. It is recommended that this leeway should
 * not be bigger than a few minutes.
 *
 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
 */
JWT::$leeway = 60; // $leeway in seconds
$decoded = JWT::decode($jwt, $config['env']['jwt_secret_key'], array('HS256'));
