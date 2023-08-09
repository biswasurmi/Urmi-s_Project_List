<?php
   
    // Start the session
    session_start();

    // Check if the user is logged in (i.e., if the required session data exists)
    $isLoggedIn = isset($_SESSION["username"]) && isset($_SESSION["password"]);
    $userRole = "";
    if ($isLoggedIn) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "new_site";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sessionEmail = $_SESSION['email'];
        $query = "SELECT role FROM user WHERE email = '$sessionEmail'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userRole = $row["role"];
        }
        $query = "SELECT description FROM user WHERE email = '$sessionEmail'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userDescription = $row["description"];
            $_SESSION["description"] = $userDescription; // Update the session with the retrieved description
        }
        $conn->close();
    }
    // Handle logout
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutBtn"])) {
        // Clear all session data and destroy the session
        session_unset();
        session_destroy();

        // Redirect to the home page or any other desired page after logout
        header("Location: home.php");
        exit;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["saveChangesBtn"])) {
        // Get the new values from the form submission or use the session values
        $newUsername = !empty($_POST["username"]) ? $_POST["username"] : $_SESSION["username"];
        $newname = !empty($_POST["name"]) ? $_POST["name"] : $_SESSION["name"];
        $newPassword = !empty($_POST["newPassword"]) ? $_POST["newPassword"] : $_SESSION["password"];
        $newEmail = !empty($_POST["email"]) ? $_POST["email"] : $_SESSION["email"];
        $newDescription = !empty($_POST["description"]) ? $_POST["description"] : $_SESSION["description"];
        // Replace the password in the session with the new password
        
        // If the user is logged in, update the password in the database
          if ($isLoggedIn) {
            // Replace with your actual database credentials
            $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "new_site";
        
            // Create connection
            $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
        
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Update password in the database for the logged-in user
            $password = $_SESSION['password'];
            $query = "UPDATE user SET username = '$newUsername', name = '$newname', password = '$newPassword', email = '$newEmail', description = '$newDescription' WHERE password = '$password'";
        
            if ($conn->query($query) === TRUE) {
                // Data updated successfully
                // Update the session data with the new values
                $_SESSION["password"] = $newPassword;
                $_SESSION["email"] = $newEmail;
                $_SESSION["username"] = $newUsername;
                $_SESSION["name"] = $newname; // Update the session variable name here
                $_SESSION["description"] = $newDescription;
                // Update the actual_name in the post table
                $updatePostQuery = "UPDATE post SET actual_name = '$newname' WHERE name = '" . $_SESSION['username'] . "'";
                $conn->query($updatePostQuery); // Execute the update query for the post table
        
                
                header("Location: see_profile.php");
                exit; // Exit to ensure no further code execution
            } else {
                // Handle the error (e.g., display an error message)
                echo "Error updating data: " . $conn->error;
            }
        
            $conn->close();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Navigation Bar Styles */
        .nav-bar {
            background-color:  #34495e;
            padding: 10px 0;
            display: flex;
            justify-content: flex-start; /* Align all buttons to the left */
            flex-wrap: wrap; /* Allow wrapping in smaller screens */
        }

        .nav-bar a {
            color: #efe3e3;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
        }

        .nav-bar a:hover {
            background-color: #2e66a2;
        }

        /* Update the logout button styles */
        .logout-btn {
            margin-left: auto; /* Move the logout button to the right */
            padding: 10px 20px; /* Add padding to match other buttons */
            background-color: #34495e; /* Apply the hover background color */
            border: none;
            color: #f1ecec;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Add a smooth transition */
        }

        .logout-btn:hover {
            background-color: #2e66a2; /* Darken the color on hover */
        }

        .dashboard-btn {
            display: none; /* Hide the dashboard button by default */
        }

        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .nav-bar {
                flex-direction: column; /* Change to vertical layout on smaller screens */
                align-items: flex-start; /* Align links to the left */
            }

            .nav-bar a {
                margin: 5px 0; /* Add space between vertical links */
            }

            .dashboard-btn {
                display: block; /* Display the dashboard button for smaller screens */
                margin: 5px 0; /* Add space between dashboard and other links */
            }
        }
        

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container:hover{
            transition-property: ;
        }
        h1 {
            text-align: center;
            color: #8f1cb1;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #8f1cb1;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #34495e;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #2e66a2;
        }

        
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="home.php">HOME</a>
        <a href="function.php">HOW IT WORKS</a>
        <a href="about.php">ABOUT</a>
        <a href="contact.php">CONTACT</a>
        
        <?php if ($isLoggedIn) : ?>
            <?php if ($userRole === "student") : ?>
                <a href="req_noti.php">RECRUITERS_NOTIFICATION</a>
                <a href="search.php">SEARCH</a>
            <?php else : ?>
                <a href="add_post.php">ADD_POST</a>
                <a href="see_post.php">SEE_POST</a>
                <a href="notifications.php">APPLICANT'S_NOTIFICATION</a>
            <?php endif; ?>
            
            <a href="see_profile.php">PROFILE</a>
            <a href="update_profile.php">UPDATE_PROFILE</a>
            <!-- Logout button is aligned to the right -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <button class="logout-btn" type="submit" name="logoutBtn">LOGOUT</button>
            </form>
        <?php else : ?>
            <a href="signin.php">SIGN IN</a>
            <a href="signup.php">SIGN UP</a>
        <?php endif; ?>
    </div>
    <div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 999;"></div>
    <div class="container">
        <h1>UPDATE PROFILE</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="updateForm">
            <div class="form-group">
                <label for="username">USERNAME</label>
                <input type="text" id="username" name="username" placeholder="<?php echo $_SESSION["username"]; ?>" >
            </div>
            <div class="form-group">
                <label for="name">NAME</label>
                <input type="text" id="name" name="name" placeholder="<?php echo $_SESSION["name"]; ?>" >
            </div>
            <div class="form-group">
                <label for="email">EMAIL</label>
                <input type="email" id="email" name="email" placeholder="<?php echo $_SESSION["email"]; ?>" >
            </div>
            <div class="form-group">
                <label for="newPassword">PASSWORD</label>
                <input type="password" id="newPassword" name="newPassword" placeholder="<?php echo $_SESSION["password"]; ?>" >
            </div>
            <div class="form-group">
                <label for="description">DESCRIPTION</label>
                <textarea id="description" name="description" rows="4" placeholder="<?php echo $_SESSION["description"]; ?>"></textarea>
            </div>
            <div id="successMessage" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); z-index: 1000;">
                Data updated successfully. <br>
                <button type="button" onclick="hideSuccessMessage()">Close</button>
            </div>
            <button type="submit" onclick="showSuccessMessage()" name="saveChangesBtn">Update</button>
        </form>
    </div>
    
    
</body>
</html>

