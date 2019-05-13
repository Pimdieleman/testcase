<?php
Class gastenboek {
	public function __construct($db) {
		$this->dblogin = $db;
		$errors = array();
	}

	function setMessage($user_id, $bericht) {
		try {
				$query = "SELECT user_id FROM Gastenboek WHERE user_id = :user_id";
				$pquery = $this->dblogin->prepare($query);

				$pquery->bindParam(":user_id", $user_id);

				$pquery->execute();

				$login_row = $pquery->fetch(PDO::FETCH_ASSOC);

				if ($pquery->rowCount() < 1) {
					try {
						$query = "INSERT INTO Gastenboek(user_id, message) values (:user_id, :bericht)";

						$pquery = $this->dblogin->prepare($query);

						$pquery->bindParam(":user_id", $user_id);
						$pquery->bindParam(":bericht", $bericht);

						$pquery->execute();
					}
					catch (PDOexception $e) {
						array_push($errors, $e->getMessage());
					}
				}
				else {
					print ("A message was already placed by this account.");
					return false;
				}
		}
		catch (PDOexception $e) {
			array_push($errors, $e->getMessage());
		}
	}
	function getMessages() {
		try {
			$query = "SELECT user_id, message, datum FROM Gastenboek order by datum";

			$pquery = $this->dblogin->prepare($query);

			$pquery->execute();

			$result = $pquery->fetchAll(PDO::FETCH_OBJ);

			$output = "<div id=messages>";
			foreach($result as $row){
    			$output .= ($row->message);

    			$output .= "<br>";
			}
			$output .= "</div>";
			return $output;
		}
		catch (PDOexception $e) {
			array_push($errors, $e->getMessage());
		}
	}
	
}
?>