<?php

error_reporting(0);

require("assets/includes/definitions.php");

if (!INSTALLED) header("Location: install.php"); // If the site is not installed redirect to the installer

require("assets/includes/functions.php");

if (!isset($_GET['alias'])) {
	die("Maybe try actually providing a parameter");
}

$alias = $_GET['alias'];

$result = $db->getURL($alias);
if ($result) header("Location: " . $result);
else header("Location: /");

?>