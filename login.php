<?
// if the "u" session key is already set and it's set to the magic word, then redirect to the main listing immediately
// if not, then show the login page
if (isset($_SESSION['u']) && $_SESSION['u'] === 'magicword') {
	header('location: index.php');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Log-in - UNAVSA-11 Secret Admirers</title>
	<? include('_header.php') ?>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="index.php">UNAVSA-11 Secret Admirers</a>
        </div>
      </div>
    </div>
	<div class="container">
      <div class="jumbotron">
          <h1 class="page-header">Log in</h1>
		  <div class="jumbotron">
			<form action="index.php" method="post">
				<p>What's the magic word?</p>
				<p><input class="form-control" id="password" type="password" name="password" /></p>
				<p><input class="btn btn-success" type="submit" name="submit" value="Log In" /></p>
			</form>
			</div>
			<script type="text/javascript">
				$("#password").focus();
			</script>
      </div>
    </div>
	<? include('_footer.php'); ?>
	</body>
</html>