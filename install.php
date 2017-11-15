<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Install Shorten.IO</title>
	<link href="assets/css/install.css" type="text/css" rel=StyleSheet>
</head>
<body>
	<main>
		<header>Install Shorten.IO</header>
		<section class="mid">

			<?php
			error_reporting(0);

			require("assets/includes/definitions.php");

			// Gracefully quit the script in case of an error
			function abort($error) {
				$print = "<p class=\"explain error\">The script couldn't finish the installation because of the following reason: <br /><br /><b>" . $error . "</b><br /><br />Please, <a href=\"install.php\">try again</a>.</p>";
				$print .= "</section>";
				$print .= "</main>";
				$print .= "</body></html>";
				die($print);
			}

			// Check if site is already installed
			if (INSTALLED) {
				abort("The site is already installed! If you want to modify the config file, you can find it in \"assets/includes/definitions.php\".");
			}

			// Where the magic happens
			if (isset($_POST['install'])) {
				// Check if the user has sent an empty query
				if (empty($_POST['sitename']) || empty($_POST['host']) || empty($_POST['user']) || empty($_POST['pass']) || empty($_POST['db'])) {
					abort("You need to fill in all the fields.");
				}

				$sitename = $_POST['sitename'];
				$host = $_POST['host'];
				$user = $_POST['user'];
				$pass = $_POST['pass'];
				$db = $_POST['db'];

				$conn = mysqli_connect($host, $user, $pass, $db);

				// Test MySQL connection
				if (mysqli_connect_errno()) {
					abort("MySQL error: " . mysqli_connect_error());
				}

				// Check if table already exists
				$check = $conn->query("SHOW TABLES LIKE 'links'");
				if ($result->num_rows > 0) {
					abort("The \"links\" table already exists. Please delete everything from the database and try running the installer again.");
				}

				// Install tables we need
				$query = "CREATE TABLE IF NOT EXISTS links ( ".
						 "   id INT NOT NULL AUTO_INCREMENT,".
						 "   alias VARCHAR(8),".
						 "   href VARCHAR(1024),".
						 "   uid INT NOT NULL DEFAULT '0',".
						 "   PRIMARY KEY (id))";
				if (!$result = $conn->query($query)) {
					abort("MySQL error: " . $conn->error);
				}

				// Replace single and double quotes with their HTML character code
				$sitename = str_replace("'", "&#39;", $sitename);
				$sitename = str_replace("\"", "&quot;", $sitename);

				// Create new definitions file
				$fname = "assets/includes/definitions.php";
				$contents = "<?php\n\n";
				$contents .= "define(\"INSTALLED\", true);\n";
				$contents .= "define(\"SITE_NAME\", \"" . $sitename . "\");\n";
				$contents .= "define(\"DB_HOST\", \"" . $host . "\");\n";
				$contents .= "define(\"DB_USER\", \"" . $user . "\");\n";
				$contents .= "define(\"DB_PASS\", \"" . $pass . "\");\n";
				$contents .= "define(\"DB_NAME\", \"" . $db . "\");\n\n";
				$contents .= "?>";
				$success = file_put_contents($fname, $contents);
				if (!$success) {
					$conn->query("DROP TABLE links");
					abort("Could not write the config file. Are you sure you've set up the right permissions?");
				}

				// Delete installer
				$del = unlink("install.php");
				if (!$del) {
					abort("The site has been installed but the installer file could not be deleted. Please remove install.php yourself.");
				}

				die("<p class=\"explain\">Installation completed! You can see your website <a href=\"/\">here</a>.</p></section></main></body></html>");
			}

			?>

			<p class="explain">
				Before you start the installation make sure to create a new MySQL user and database you want to use for the link shortener. Then provide the details in the following form and click <b>Install</b> to start the installation.
			</p>
			<form action="install.php" method="post" name="install">
				<div class="row"><label for="sitename">Site Name: </label><input type="text" name="sitename" id="sitename" placeholder="e.g.: Shorten.IO"></div>
				<div class="row"><label for="host">MySQL host: </label><input type="text" name="host" id="host" placeholder="e.g.: localhost or 127.0.0.1"></div>
				<div class="row"><label for="user">Username: </label><input type="text" name="user" id="user" placeholder="e.g.: shortenio_user"></div>
				<div class="row"><label for="pass">Password: </label><input type="password" name="pass" id="pass"></div>
				<div class="row"><label for="db">Database name: </label><input type="text" name="db" id="db" placeholder="e.g.: shortenio_db"></div>
				<div class="submit-button">
					<input type="submit" value="Install" name="install">
				</div>
			</form>
		</section>
	</main>
</body>
</html>