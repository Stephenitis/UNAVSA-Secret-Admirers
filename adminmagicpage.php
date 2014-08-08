<?php
require_once('_config.php');

// NOTE: ideally this page should be protected in some shape or form (e.g. password or IP check)

// check if the "d" parameter is set (to delete a confession)
if (isset($_GET['d'])) {
	// ensure the "d" parameter contains a value
	if ($_GET['d']) {
		// build the SQL query, sanitizing the "d" parameter
		$sql = "DELETE FROM unavsalove14 WHERE id = '".addslashes($_GET['d'])."'";
		// connect to the database
		mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
		@mysql_select_db($DB_NAME) or die("Unable to select database");
		// execute the delete query
		$result = mysql_query($sql) or die(mysql_error());
		// redirect the user back to the admin page
		header('location: adminmagicpage.php');
		exit;
	}
}

// check to see if any checkboxes were ticked (to approve a confession/confessions)
if (isset($_POST['checkbox'])) {
	$checkbox = $_POST['checkbox'];
	// use PHP's implode function to convert the above array to a a comma, quoted separated string, to cleanly build our SQL query of confession IDs (e.g. ('1','3','5');)
	$id = "('" . implode( "','", $checkbox ) . "');" ;
	// build the SQL query, including the above list of IDs to approve
	$sql = "UPDATE unavsalove14 SET approved = '1' WHERE id IN $id";
	// connect to the database and run the query
	mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
	@mysql_select_db($DB_NAME) or die("Unable to select database");
	$result = mysql_query($sql) or die(mysql_error());
	// delete all 3 cache files (so that the main page and cache files are re-generated the next time the main listing is accessed)
	// - @ is prepended to the unlink function to suppress any warning/error messages if the below cache files don't exist (yet)
	@unlink('old.cache');
	@unlink('new.cache');
	@unlink('list.cache');
	// redirect the user to the admin page (to avoid re-POSTing if the user refreshes the page)
	header('location: adminmagicpage.php');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Admin - UNAVSA-11 Secret Admirers</title>
	<? include('_header.php') ?>
	<style type="text/css">
	#people {
		border-top: 1px solid #eee;
		border-left: 1px solid #eee;
	}
	#people td, #people th {
		border-right: 1px solid #eee;
		border-bottom: 1px solid #eee;
		padding: 5px;
		vertical-align: top;
	}
	</style>
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
            <li><a href="add.php">Add a Confession</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="add.php">Admin</a></li>
            <li><a href="index.php?l=1">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
          <h1 class="page-header">Admin</h1>
<?
// generate the list of confessions to approve

// connect to the database
mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
@mysql_select_db($DB_NAME) or die("Unable to select database");
// query the database for unapproved confessions, sorted by oldest to newest
$results = mysql_query("SELECT * FROM `unavsalove14` WHERE approved = '0' ORDER BY submitted");
// get total number of unapproved confessions
$total = mysql_num_rows($results);
echo '<h2>'.$total.' confessions</h2>';
// encapsulate all returned rows in a form (for the checkmarking + approving)
echo '<form action="" method="post"><input type="button" value="Check All" onclick="clickall()" /> <input type="submit" value="Approve Checked" />';
// output the results from the query
echo '<table cellspacing="0" cellpadding="0" border="0" id="people"><thead><tr><th>Addressed To</th><th>Confession</th><th>Submitted</th><th>IP</th><th>Approve?</th><th>Delete?</th></tr></thead><tbody>';
// loop through results
while ($row = mysql_fetch_array($results)) {
	echo '<tr><td>'.htmlentities(stripslashes($row['firstname'])).' '.htmlentities(stripslashes($row['lastname'])).'</td><td>'.nl2br(htmlentities(stripslashes(trim($row['message'])))).'</td><td>'.date('Y/m/d H:i:s', $row['submitted']).'</td><td>'.$row['ip'].'</td><td><input name="checkbox[]" type="checkbox" id="checkbox[]" value="'.$row['id'].'"></td><td><a href="?d='.$row['id'].'">Delete</a></td></tr>';
}
echo '</tbody></table><input type="button" value="Check All" onclick="clickall()" /> <input type="submit" value="Approve Checked" /></form>';
?>
			</div>
		  <div class="row">
			<p>&nbsp;</p>
			<p><strong>Disclaimer: not an official UNAVSA system. Idea by <a href="https://www.facebook.com/andyt.pham.7?fref=nf" target="_blank">Andy Pham</a>; Coded + hosted by <a href="https://www.facebook.com/ndrwy" target="_blank">Andrew Young</a>.</strong></p>
		  <p>No data will be given to third-party entities.</p>
		</div>
      </div>
	<? include('_footer.php'); ?>
	<script type="text/javascript">
		// a custom function to make my life easier ;) (tick all)
		function clickall() {
			$('input[type="checkbox"]').each(function() {
				this.checked = true;                        
			});
		}
	</script>
	</body>
</html>