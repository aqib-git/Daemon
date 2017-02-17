<?php
require_once('config.php');
require_once('helpers.php');
if(!isset($_POST['submit'])) {
	header('location: index.php');
}
if(exceedsLimit(MAX_PROCESS, $dbh)) {
	die('too many processes already running');
}
$current_ip = $_SERVER['REMOTE_ADDR'];
$resultSet = ipRecordExists($dbh, $current_ip);
if($resultSet  !== false) {
	$id = filter_var($_GET['id'], FILTER_SANITIZE_STRING);
	$start = (int)filter_var($_GET['start'], FILTER_SANITIZE_NUMBER_INT);
	if(empty($id)) {
		die("post request must have id field");
	}
	if(!isset($start)) {
		die("post request must have start field");
	}
	if($id !== $resultSet['url_hash']){
		die("id value is invalid");
	}
	$processPath = '/proc/'.$resultSet['pid'];
	if(file_exists($processPath) && $start === 0) {
		killProcess($resultSet['pid']);		
		$query = 'UPDATE scripts SET status = ? WHERE id = ?';
		$stmt = $dbh->prepare($query);
		$stmt->execute(array(0, $resultSet['id']));
	} else if(!file_exists($processPath) && $start === 1) {
		$pid = createProcess($_POST['params']);
		$query = 'UPDATE scripts SET status = ?, pid = ?, params = ? WHERE id = ?';
		$stmt = $dbh->prepare($query);
		$stmt->execute(array(1, $pid, $_POST['params'], $resultSet['id']));
	}
	header('location: index.php?id='.$resultSet['url_hash']);
} else {
	//when new ip requests
	$salt = randomString(16);
	$url_hash = md5($salt+$current_ip);
	$params = $_POST['params'];
	$status = true;
	$pid = createProcess($_POST['params']);
	$query = "INSERT INTO scripts (url_hash, pid, params, ip, status, salt) VALUES (?, ?, ?, ?, ?, ?)";
	$stmt = $dbh->prepare($query);
	$result = $stmt->execute(array($url_hash, $pid, $params, $current_ip, $status, $salt));
	header('location: index.php?id='.$url_hash);
}
