<?php
require_once('_config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Home - UNAVSA-11 Secret Admirers</title>
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
	thead { background-color: #ccc; }
	.odd { background-color: #eee; }
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
            <li class="active"><a href="index.php">Home</a></li>
            <li><a href="add.php">Add a Confession</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="index.php?l=1">Logout</a></li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" id="search" class="form-control" placeholder="Search...">
          </form>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
          <h1 class="page-header">Home</h1>
		  <p><strong>Got someone on your mind? Add a Confession</strong>!</p>
		  <p><a href="add.php" class="btn btn-success">Add a Confession</a></p>
		  <p class="text-right">Sort by: <strong><a href="index.php">Name</a> | <a href="?t=o">Oldest First</a> | <a href="?t=n">Newest First</a></strong></p>
<?
// check how the user wants to sort the list
if ($_GET['t'] == 'o') $file = 'old';
else if ($_GET['t'] == 'n') $file = 'new';
else $file = 'list';

// if the sort type cache file exists, then display it to avoid needless database querying
if (file_exists($file.'.cache')) {
	include($file.'.cache');
} else {
// if the cache file doesn't exist, then we'll need to query the database
	// connect to the database
	mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
	@mysql_select_db($DB_NAME) or die("Unable to select database");
	
	// depending on how the user wants to sort the list, query accordingly
	if ($file == 'old') $results = mysql_query("SELECT * FROM `unavsalove14` WHERE approved = '1' ORDER BY submitted ASC"); // sort by oldest to newest
	else if ($file == 'new') $results = mysql_query("SELECT * FROM `unavsalove14` WHERE approved = '1' ORDER BY submitted DESC"); // sort by newest to oldest
	else $results = mysql_query("SELECT * FROM `unavsalove14` WHERE approved = '1' ORDER BY firstname, lastname, submitted"); // sort by name
	
	// initiate the output buffer (for caching)
	ob_start();
	
	// get the total of rows returned by the query
	$total = mysql_num_rows($results);
	
	// display the total number of confessions and also a hidden "searchresults" container (which would be shown with the number of matches when the user searches)
	echo '<h2>'.$total.' confessions<span id="searchresults"></span></h2>';
	echo '<table cellspacing="0" cellpadding="0" border="0" id="people"><thead><tr><th>Addressed To</th><th>Confession</th><th>Submitted</th></tr></thead><tbody>';
	// create a variable to store all "Unknown" type confessions (to display at the end of the table
	$unknown = '';
	// loop through all rows
	while ($row = mysql_fetch_array($results)) {
		// if the current row is an "Unknown" type, then append it to the initiated $unknown variable
		if ($row['firstname'] == 'Unknown' && $row['lastname'] == 'Unknown') {
			$unknown .= '<tr rel="unknown"><td>**Unknown**</td><td>'.nl2br(htmlentities(stripslashes(trim($row['message'])))).'</td><td>'.date('Y/m/d H:i:s', $row['submitted']).'</td></tr>';
		} else {
		// else, it is a normal confession with a first and last name
			echo '<tr rel="'.strtolower($row['firstname']).strtolower($row['lastname']).'"><td>'.htmlentities(stripslashes($row['firstname'])).' '.htmlentities(stripslashes($row['lastname'])).'</td><td>'.nl2br(htmlentities(stripslashes(trim($row['message'])))).'</td><td>'.date('Y/m/d H:i:s', $row['submitted']).'</td></tr>';
		}
		// note for the above: for the search feature, each table row (tr) is assigned a "rel" attribute with the person's first and last names as its value (converted to lowercase)
		// - the output is sanitized (namely the htmlentities function to escape HTML code) to ensure there is no XSS business going on (cross-site scripting attacks).
		// - stripslashes is also used to avoid showing things like "I\'m in love with you."
	}
	// display all "Unknown" type confessions at the end of the table
	echo $unknown;
	echo '</tbody></table>';
	// store echo'd output in a variable (note: since PHP's output buffer was initiated, the content above isn't actually echo'd, it's just stored in the buffer, hence the below line to store it in a variable)
	$list = ob_get_contents();
	// end output buffer capturing
	ob_end_clean();
	// display output
	echo $list;
	// store output in a file (for caching purposes)
	file_put_contents($file.'.cache', $list);
}
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
			// function to add the zebra stripes to the table for increased readability
			function reinit() {
				$("#people tbody tr").removeClass("odd");
				$("#people tbody tr:visible:odd").addClass("odd");
			}
			// on page load, strip the table rows
			reinit();
			// search function - bind on key up (whenever a user types into the search box, this function will rn)
			$("#search").keyup(function() {
				// check if the search box has something inputted by the user
				if ($(this).val()) {
					// hide all confessions
					$("#people tbody tr").hide();
					// go through all rows, checking the "rel" attribute to see if it contains (*=) anything the user has typed in (converted to lowercase + any non-alphanumeric (\W+) characters are removed)
					var matches = $("#people tbody tr[rel*='"+$(this).val().toLowerCase().replace(/\W+/g, '')+"']"); // store matches in a variable as we will be reusing it
					// show all confessions that match the entered search word
					matches.show();
					// purely aesthetics, but by default make the word "matches" after
					var lang = 'es';
					// if there is only 1 match, then remove the "es" (so it is "1 match" instead of "1 matches")
					if (matches.length == 1) lang = '';
					// display the number of matches
					$("#searchresults").html(" ("+matches.length+" match"+lang+")");
				} else {
					// if the search box is blank, then show all confessions
					$("#people tbody tr").show();
					// hide number of matches
					$("#searchresults").html("");
				}
				// after every key stroke, re-stripe the confessions rows as different rows will be hidden/shown
				reinit();
			});
		</script>
	</body>
</html>