<?php

function createProcess($params) {
	exec("screen -m -d ".COMMAND." ".SCRIPT_PATH.SCRIPT_FILE." ".escapeshellarg($params));
	exec("ps aux | grep -i [S]CREEN | grep -F ".SCRIPT_PATH." | grep ".SCRIPT_FILE, $output);
	$pid = 0;
	$pidCount = count($output);
	if($pidCount == 0) {
		return $pid;
	}	
	$pid = preg_split('/ +/', $output[$pidCount-1])[1];
	return $pid;
}

function killProcess($pid) {
	exec("pkill -TERM -P ".$pid);
}

function ipRecordExists($dbh, $ip) {
	$query = 'SELECT *  FROM scripts WHERE ip = ?';
	$stmt = $dbh->prepare($query);
	$stmt->execute(array($ip));
	$resultSet = $stmt->fetch(PDO::FETCH_ASSOC);
	return empty($resultSet) ? false : $resultSet;
}

function randomString($length = 16) {
	$str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}

function exceedsLimit($limit, $dbh) {
	$query = 'SELECT COUNT(*) FROM scripts';
	$res = $dbh->query($query);
	return $res->fetchColumn() > $limit;
}