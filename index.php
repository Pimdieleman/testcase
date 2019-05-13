<?php
session_start();
require 'class.login.php';
require 'class.gastenboek.php';
(array)$errors;
//Database connect via PDO
$db = new PDO(	'mysql:host=localhost;dbname=iceshop;charset=utf8mb4',
				'root',
                '%1993Pd21',
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => false
                )
             );
$Login = New Login($db);

if (isset($_GET['logout'])) {
	$Login->log_out();
}

if ($Login->is_logged_in()) {
	$gastenboek = New gastenboek($db);
}
if (isset($_POST['gastenboek'])) {
	$message = $_POST['message'];
	if (empty($message)) {
		array_push($errors, "Please enter a message.");
	}
	elseif ($gastenboek->setMessage($_SESSION['user_session'], $message)) {
		echo "Bericht geplaatst";
	}
	else {
		array_push($errors, "A message was already placed.");
	}

}
if (isset($_POST['register'])) {
    // Retrieve form input
    $username = trim($_POST['user_name']);
    $password = trim($_POST['user_password']);

    // Check for empty and invalid inputs
    if (empty($username)) {
        array_push($errors, "Please enter a valid username.");
    } elseif (empty($password)) {
        array_push($errors, "Please enter a valid password.");
    }
    elseif ($Login->register($username, $password)) {
    	echo "Registered";
    }
    else {
    	array_push($errors, "An unknown registration error has occured.");
    }
}

if (isset($_POST['login'])) {
    // Retrieve form input
    $user_name = trim($_POST['user_name']);
    $user_password = trim($_POST['user_password']);

    // Check for empty and invalid inputs
    if (empty($user_name)) {
        array_push($errors, "Please enter a valid username.");
    } elseif (empty($user_password)) {
        array_push($errors, "Please enter a valid password.");
    }
    else {
	    if ($Login->login($user_name, $user_password)) {
	    	$Login->redirect('index.php');
	    }
	    else {
	    	array_push($errors, "Incorrect login credentials");
	    }
	}
}


    /*
//Test querry to test database
$handle = $db->prepare('select username from Users where user_id = :user_id or username = :username limit 2');

//Initiate variable for user ID
$user_id = 2;
$username = 'Pim_Dieleman';

//Binding paras with variables
$handle->bindParam(':user_id', $user_id);
$handle->bindParam(':username', $username);

//Executing queries
$handle->execute();

//Fetch result
$result = $handle->fetchAll(PDO::FETCH_OBJ);

//Create export HTML
$output = "<div>";

foreach($result as $row){
    $output .= ($row->username);
    $output .= "<br>";
}

$output .= "</div>";

//Print output
print($output);
*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OOP PHP ICESHOP - Login and Register</title>
</head>
<body>
    <h1>Welcome</h1>
    <?php 
    if ($Login->is_logged_in()) {
    	print ('<form action="index.php" method="POST">
    	<label for="message">Message:</label>
        <input type="text" name="message" id="message" required>

        <input type="submit" name="gastenboek" value="Plaatsen">
    </form>');

    	print ($gastenboek->getMessages());
    	print('<a href="?logout=true">Log out</a>');
    }
    else {
    	print ('<h2>Log in</h2>
    <form action="index.php" method="POST">
        <label for="user_name">E-mail Address:</label>
        <input type="text" name="user_name" id="user_name" required>

        <label for="user_password_log_in">Password:</label>
        <input type="password" name="user_password" id="user_password" required>

        <input type="submit" name="login" value="Log in">
    </form>
    
    <h2>Register</h2>
    <form action="index.php" method="POST">
        <label for="user_name">Email:</label>
        <input type="text" name="user_name" id="user_name" required>

        <label for="user_password">Password:</label>
        <input type="password" name="user_password" id="user_password" required>

        <input type="submit" name="register" value="Register">
    </form>');
    }
?>
</body>
</html>