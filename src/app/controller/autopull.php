<?php

die('No access');

$token = isset($_url[1]) ? $_url[1] : null;


if ($token != "xgbar3p6369gl43x")
	die('No access');




//edit with your data
$pullLogFile = logdir."/pull.log";
$repo_dir = '~/repository.git';
$web_root_dir = '/var/www';
$post_script = "$web_root_dir/start.sh";
$onbranch = 'master';


// Full path to git binary is required if git is not in your PHP user's path. Otherwise just use 'git'.
$git_bin_path = 'git';


$update = false;
$payload = json_decode( file_get_contents( 'php://input' ), true );


//file_put_contents($pullLogFile, "POST CONTENT: ".print_r($payload, true));


if(empty($payload)) {


  file_put_contents($pullLogFile, date('m/d/Y h:i:s a') . " File accessed with no data\n", FILE_APPEND) or die('log fail');
  die("<img src='http://loremflickr.com/320/240' />");


}


if ( isset( $payload['push'] ) ) {


  $lastChange = $payload['push']['changes'][ count( $payload['push']['changes'] ) - 1 ]['new'];
  $branch     = isset( $lastChange['name'] ) && ! empty( $lastChange['name'] ) ? $lastChange['name'] : '';


  if($branch == $onbranch){
    $update = true;
  }


}


if ($update) {


  // Do a git checkout to the web root
  exec('cd ' . $repo_dir . ' && ' . $git_bin_path  . ' fetch');
  exec('cd ' . $repo_dir . ' && GIT_WORK_TREE=' . $web_root_dir . ' ' . $git_bin_path  . ' checkout -f');


  // Log the deployment
  $commit_hash = shell_exec('cd ' . $repo_dir . ' && ' . $git_bin_path  . ' rev-parse --short HEAD');
  //echo "Deployed branch: " .  $branch . " Commit: " . $commit_hash . "\n";
  file_put_contents($pullLogFile, date('m/d/Y h:i:s a') . " Deployed branch: " .  $branch . " Commit: " . $commit_hash . "\n", FILE_APPEND);


  if(file_exists($post_script)){
    exec('chmod +x '.$post_script);
    exec('sh '.$post_script. " > /dev/null &");
  }


}
die("DONE");