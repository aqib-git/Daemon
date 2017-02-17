<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'python_db');
define('DB_USER', 'root');
define('DB_PASSWORD', 'password');
define('DB_DRIVER', 'mysql');
define('SCRIPT_PATH', '.');
define('SCRIPT_FILE', 'sleepy.py');
define('MAX_PROCESS', 10);

/*connect to database*/
try {
    $dbh = new PDO(
    	DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME,
    	DB_USER, 
    	DB_PASSWORD
    );
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

/* create scripts table if doesn't exist */
function createScriptsTable($dbh) {
	$query = 'CREATE TABLE IF NOT EXISTS `scripts` (
	`id` INT AUTO_INCREMENT NOT NULL,
	`url_hash` varchar(200) NOT NULL,
	`pid` INT NOT NULL,
	`params` TEXT,
	`ip` varchar(200) NOT NULL,
	`status` boolean,
	`salt` varchar(16),
	PRIMARY KEY (`id`),
	UNIQUE (`pid`),
	UNIQUE (`ip`),
	UNIQUE (`url_hash`))';
	$dbh->exec($query);
}

createScriptsTable($dbh);