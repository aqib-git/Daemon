<?php

function createProcess($params) {
	exec("screen -m -d python ".SCRIPT_PATH."/".SCRIPT_FILE." ".escapeshellarg($params));
	exec("ps aux | grep ".SCRIPT_FILE ,$output);
	$pid = 0;	
	for($i = 0, $len = count($output); $i < $len-1; $i++){
		if(strpos($output[$i],'SCREEN') !== false){
				$pid = preg_split('/ +/', $output[$i])[1];
		}
	}
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