<?php

error_reporting(0);

require("assets/includes/definitions.php");

if (!INSTALLED) header("Location: install.php"); // If the site is not installed redirect to the installer

require("assets/includes/functions.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?= SITE_NAME ?> Link Shortener</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0, user-scalable=no">
	<link type="text/css" href="assets/css/style.css" rel="StyleSheet">
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>
	<main>
		<header><?= SITE_NAME ?></header>
		<section class="link-creator">
			<form action="/" method="post" name="create">
				<input type="text" placeholder="Insert any link to shorten!" name="link">
				<div class="submit-container"><input type="submit" value="Shorten!" name="create"></div>
			</form>
			<?php

			if (isset($_POST['create'])) {
				$result = $db->addURL($_POST['link']);
				
				// Error handling and insertion
				if (!$result) {
					if (isset($_SESSION['error'])) {
						echo '<div class="explain error">Aw, snap! Something bad happened. ' . $_SESSION['error'] . '</div>';
						unset($_SESSION['error']);
					} else {
						echo '<div class="explain error">Unhandled exception.</div>';
					}
				} else {
					echo '<div class="url-container">Here\'s your short URL: <b class="url" data-clipboard-text="' . siteURL() . '/' . $result . '">' . siteURL() . '/' . $result . '</b></div>';
				}
			}

			?>
		</section>
		<footer><?= date('Y') ?> &copy; <?= SITE_NAME ?></footer>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js"></script>
		<script type="text/javascript">
			var copylink = new Clipboard(".url");
			var originalText = $(".url").text();
			copylink.on('success', function(e) {
				$(".url").text("Copied to clipboard!");
			    setTimeout(function() {
			    	$(".url").text(originalText);
			    }, 3000);
			});
		</script>
	</main>
</body>
</html>