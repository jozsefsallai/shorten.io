<?php

// Check if entered href is a valid URL 
function urlcheck($url) {
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	if (!filter_var($url, FILTER_VALIDATE_URL)) return false;
	return $url;
}

// Error handling
function error($errorstring) {
	session_start();
	$_SESSION['error'] = $errorstring;
}

// Get the base URL of the project
function siteURL() {
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') $protocol = "https";
	else $protocol = "http";
	return $protocol . "://" . $_SERVER['SERVER_NAME'];
}

// Database handler class
class dbHandler {

	private $conn;
	public $connected = false;

	function __construct() {
		if (!@require("definitions.php")) die("Definitions file doesn't exist. Try reinstalling.");
		require("definitions.php");
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (mysqli_connect_errno()) {
			die("MySQL error: " . mysqli_connect_error());
		} else $this->connected = true;
	}

	function __destruct() {
		if ($this->connected) $this->conn->close();
	}

	// Generare random 8 characters long alias using Latin characters and digits
	private function generateAlias() {
		$allowed = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charLen = strlen($allowed);
		$alias = '';
		for ($i = 0; $i < 8; $i++) {
			$alias .= $allowed[rand(0, $charLen - 1)];
		}
		return $alias;
	}

	// Add URL to the database
	public function addURL($url) {
		if (!urlcheck($url)) {
			error("The entered URL is invalid.");
			return false;
		}

		// Check if random string is not an existing alias in the database
		$goforit = false;
		$alias;
		while (!$goforit) {
			$alias = $this->generateAlias();
			$query = "SELECT * FROM `links` WHERE `alias`='$alias'";
			$result = $this->conn->query($query);
			if (!$result) {
				error("MySQL error: " . $this->conn->error);
				return false;
			}
			if ($result->num_rows == 0) $goforit = true;
		}

		// If everything's ok proceed to insertion
		$stmt = $this->conn->prepare("INSERT INTO `links` (alias, href) VALUES (?, ?)");
		$stmt->bind_param('ss', $alias, urlcheck($url));
		$result = $stmt->execute();
		if (!$result) {
			error("MySQL error: " . $this->conn->error);
			return false;
		}

		return $alias; // Return the generated alias  
	}

	public function getURL($alias) {
		$alias = $this->conn->real_escape_string($alias);
		$query = "SELECT href FROM `links` WHERE `alias`='$alias'";
		$result = $this->conn->query($query);
		if (!$result) return false;
		$result_arr = $result->fetch_array();
		return $result_arr[0];
	}

}

$db = new dbHandler();