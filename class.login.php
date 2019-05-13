<?php

Class login {
	private $dblogin;

	public function __construct($db) {
		$this->dblogin = $db;
	}
	public function register ($username, $password) {
		try {
			$hash_password = password_hash($password, PASSWORD_DEFAULT);
			$query = "INSERT INTO Users(username, password) values (:username, :password)";
			$pquery = $this->dblogin->prepare($query);

			$pquery->bindParam(":username", $username);
			$pquery->bindParam(":password", $hash_password);

			$pquery->execute();
		}
		catch (PDOexception $e) {
			array_push($errors, $e->getMessage());
		}
	}
	public function login($username, $password) {
		try {
			$query = "SELECT user_id, username, password FROM Users WHERE username = :username";
			$pquery = $this->dblogin->prepare($query);

			$pquery->bindParam(":username", $username);

			$pquery->execute();

			$login_row = $pquery->fetch(PDO::FETCH_ASSOC);

			if ($pquery->rowCount() > 0) {
				if (password_verify($password, $login_row['password'])) {
					$_SESSION['user_session'] = $login_row['user_id'];
					return true;
				}
				else {
					return false;
				}
			}
		}
		catch (PDOexception $e) {
			array_push($errors, $e->getMessage());
		}
	}
	public function is_logged_in() {
		if(isset($_SESSION['user_session'])) {
			return true;
		}
	}
	public function redirect($url) {
		header("Location: $url");
	}
	public function log_out() { 
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }
}
?>