<?php
require_once('_config.php');

// check to see if the user has submitted a confession
if (isset($_POST['submit'])) {
	// a function to get the user's current IP address
	function getIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip=$_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		else $ip=$_SERVER['REMOTE_ADDR'];
		return $ip;
	}
	// loop through every POST'ed key and value
	foreach($_POST as $key => $value) {
		// trim it and check if it still has something (to make sure it's just not a bunch of tabs/spaces)
		if (trim($value)) {
			// sanitize the value (re:_config.php), and store it as $(keyname) (e.g. $_POST['firstname'] => $firstname)
			$$key = sanitize($value);
		}
	}
	// if the Unknown checkbox is ticked, then set the first and last name to "Unknown"
	if (isset($_POST['unknown'])) {
		$firstname = 'Unknown';
		$lastname = 'Unknown';
	}
	// make sure the first + last names and confession are filled in
	if (isset($firstname) && isset($lastname) && isset($comment)) {
		// get current timestamp (UNIX format)
		$now = time();
		// get the user's IP address
		$ip = getIp();
		
		// connect to the database
		mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
		@mysql_select_db($DB_NAME) or die("Unable to select database");
		// insert the confession into the database
		$results = mysql_query("INSERT INTO `unavsalove14` (firstname, lastname, message, ip, submitted) VALUES ('".$firstname."', '".$lastname."', '".$comment."', '".$ip."', '".$now."')");
		// redirect the user to the Add Confession page with the s parameter set to 1 (to show a success message)
		header('location: add.php?s=1');
		exit;
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Add a Confession - UNAVSA-11 Secret Admirers</title>
	<? include('_header.php') ?>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">UNAVSA-11 Secret Admirers</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.php">Home</a></li>
            <li class="active"><a href="add.php">Add a Confession</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="index.php?l=1">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
          <h1 class="page-header">Add a Confession</h1>
		<?
			// if the s parameter is set to 1, show a success message
			if ($_GET['s'] == '1') {
				echo '<p class="bg-success" style="padding: 10px;"><strong>Confession successfully submitted! (pending approval)</strong></p>';
			}
		?>
		  <p><strong>If you are unsure of a person's name, please check <a href="https://www.facebook.com/groups/280173228842092/members/" target="_blank">the members of the UNAVSA-11 Friend Finder</a> :)</strong></p>
		  <p>Your identity will be kept secret ;)</p>
		  <p class="bg-warning" style="padding: 10px;">Note that there will be a delay between when you submit a confession and when it appears; there is an approval process to ensure no hurtful or inappropriate messages are posted.</p>
			<div class="row">
				<form class="form-horizontal" role="form" method="post" onsubmit="return validate();">
				  <div class="form-group toggleunavsa">
					<label for="firstname" class="col-sm-2 control-label">First name of who you're confessing to</label>
					<div class="col-sm-10">
					  <input type="text" name="firstname" class="form-control" id="firstname">
					</div>
				  </div>
				  <div class="form-group toggleunavsa">
					<label for="lastname" class="col-sm-2 control-label">Last name of who you're confessing to</label>
					<div class="col-sm-10">
					  <input type="text" name="lastname" class="form-control" id="lastname">
					</div>
				  </div>
				  <div class="form-group">
					<label for="unknown" class="col-sm-2 control-label">... but I don't know their name!</label>
					<div class="col-sm-10">
					  <input type="checkbox" name="unknown" id="unknown" value="1">
					</div>
				  </div>
				  <div class="form-group">
					<label for="comment" class="col-sm-2 control-label">Confession</label>
					<div class="col-sm-10">
					  <textarea name="comment" class="form-control" rows="3" id="comment"></textarea>
					</div>
				  </div>
				  <div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
					  <input type="submit" name="submit" value="Add" class="btn btn-success">
					</div>
				  </div>
				</form>
			</div>
      </div>
	  <div class="row">
		  <p>&nbsp;</p>
			<p><strong>Disclaimer: not an official UNAVSA system. Idea by <a href="https://www.facebook.com/andyt.pham.7?fref=nf" target="_blank">Andy Pham</a>; Coded + hosted by <a href="https://www.facebook.com/ndrwy" target="_blank">Andrew Young</a>.</strong></p>
		  <p>No data will be given to third-party entities.</p>
	  </div>
    </div>
	<? include('_footer.php'); ?>
	<script type="text/javascript">
		// if the "... but I don't know their name!" tickbox is checked, then show/hide the first + last name rows accordingly
		$("#unknown").click(function() {
			if ($(this).prop('checked')) {
				$(".toggleunavsa").hide();
			} else {
				$(".toggleunavsa").show();
			}
		});
		// on form submit, make sure that the required fields are filled in
		function validate(){
			if (!$('#unknown').prop('checked') && ($('#firstname').val() == '' || $('#lastname').val() == '' || $('#comment').val() == '')) {
				alert('You must fill in all three fields!');
				if ($('#firstname').val() == '') $('#firstname').focus();
				else if ($('#lastname').val() == '') $('#lastname').focus();
				else if ($('#comment').val() == '') $('#comment').focus();
				return false;
			}
			if ($('#unknown').prop('checked') && $('#comment').val() == '') {
				alert('You must enter a confession!');
				$('#comment').focus();
				return false;
			}
			return true;
		}
	</script>
	</body>
</html>