<?php
// set database info
$DB_NAME = 'dbname';
$DB_USER = 'dbusername';
$DB_PASSWORD = 'dbpassword';
$DB_HOST = 'dbhost';

// custom function I wrote to sanitize submitted form data before inserting into the database (to mitigate SQL injection attacks (e.g. ' OR 1=1 --))
function sanitize($str) {
	return addslashes(trim($str));
}

// ------------- Authentication
// ensure the magic word is entered, and is correct. Create and use a PHP Session.
session_start();
// if the "l" parameter exists and is set to 1, then delete the "u" session key + value, effectively logging out the user
if (isset($_GET['l'])) {
	if ($_GET['l'] == '1') {
		unset($_SESSION['u']);
		header('location: index.php');
		exit;
	}
}
// handle any login attempt (referencing the "password" form field)
if (isset($_POST['submit'])) {
	if ($_POST['password'] == 'magicword') {
		$_SESSION['u'] = 'magicword';
		header('location: index.php');
		exit;
	}
}
// if the "u" session key doesn't exist OR it does and it's not set to the correct word, then redirect the user to the login page 
if (!isset($_SESSION['u']) || (isset($_SESSION['u']) && $_SESSION['u'] != 'magicword')) {
	header('location: login.php');
	exit;
}
?>