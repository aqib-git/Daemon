<?php
require_once('config.php');
require_once('helpers.php');

/* check records for this ip in scripts table */
$current_ip = $_SERVER['REMOTE_ADDR'];
$resultSet = ipRecordExists($dbh, $current_ip);

if($resultSet !== false) {
	//if process was already started for this ip
	if(empty($_GET['id'])) {
		die("please provide hash by appending '?id=HASH_VALUE' to current url.");
	} else if($_GET['id'] === $resultSet['url_hash']){
		if(!file_exists('/proc/'.$resultSet['pid'])) {
			//if process was killed
			$query = 'UPDATE scripts SET status = ? WHERE id = ?';
			$stmt = $dbh->prepare($query);
			$stmt->execute(array(0, $resultSet['id']));
			//update status
			$resultSet['status'] = 0;
			$stopmessage = "Process was stopped or completed";
			$queryString= "?id=".$_GET['id'].'&start=1';
			$queryString = htmlspecialchars($queryString, ENT_QUOTES, "UTF-8");
		} else {
			$queryString="?id=".$_GET['id'].'&start=0';
			$queryString = htmlspecialchars($queryString, ENT_QUOTES, "UTF-8");
		}
		$showLink = true;
	} else {
		die("id parameter is invalid");
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Python Scripts</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</script>
</head>
<body>
<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1" style="margin-top: 100px;">
	<h4>Start/Stop a python process with parameters as input.</h4>
	<form method="post" action="process.php<?php echo empty($queryString) ? '' : $queryString; ?>">
		<div class="form-group">
			<input class="form-control" name="params" value="<?php echo empty($resultSet) ? '' : $resultSet['params']; ?>" placeholder="Enter python script params" required>
		</div>
		<div class="form-group">
			<input type="submit" class="form-control btn <?php echo empty($resultSet) ? 'btn-primary' : ($resultSet['status'] ? 'btn-danger' : 'btn-primary'); ?> " name="submit" value="<?php echo empty($resultSet) ? 'start' : ($resultSet['status'] ? 'stop' : 'start'); ?>">
		</div>
		<div class="form-group">
			<?php if(!empty($stopmessage)){?>
			<div class="alert alert-info">
				<?php echo $stopmessage;?>
			</div>
			<?php } ?>
		</div>
	</form>
	<?php if(!empty($showLink)) { ?>
	 Process link : http://<?php echo $_SERVER['REMOTE_ADDR'].'?id='.htmlspecialchars($_GET['id'], ENT_QUOTES, "UTF-8");?> (save this link to access this process next time)
	<?php } ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
</script>
</body>
</html>
