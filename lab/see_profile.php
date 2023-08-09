<?php
    // Start the session
    session_start();

    // Check if the user is logged in (i.e., if the required session data exists)
    $isLoggedIn = isset($_SESSION["username"]) && isset($_SESSION["password"]);

    // Handle logout
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logoutBtn"])) {
        // Clear all session data and destroy the session
        session_unset();
        session_destroy();

        // Redirect to the home page or any other desired page after logout
        header("Location: home.php");
        exit;
    }
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
        
        $conn->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    /* Navigation Bar Styles */
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
    .main_body {
        background-color: #f5f5f5;
        padding: 20px;
        border: 1px solid #eae9f1;
        border-radius: 5px;
        margin: 20px;
    }

    .main_header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }


    .header_text {
        font-size: 20px;
        font-weight: bold;
        align-items: center;
        color:#8f1cb1;
    }

    label {
        font-weight: bold;
        margin-right: 10px;
        color:#8f1cb1;
        padding:10px;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #8f1cb1;
        border-radius: 3px;
        margin-bottom: 10px;
        color:black;
        
    }

    textarea {
        height: 100px;
    }


    </style>
    
</head>
<body >
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
    <form>
        <div class="main_body">
            
            <label for="username">USERNAME:</label>
            <input type="text" id="username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Your name'; ?>">
            
            <label for="name">NAME:</label>
            <input type="text" id="name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : $_SESSION['username']; ?>">

            <label for="email">EMAIL:</label>
            <input type="text" id="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Your email'; ?>">

            <label for="description">DESCRIPTION:</label>
            <textarea id="description"><?php echo isset($_SESSION['description']) ? $_SESSION['description'] : 'Write your message here'; ?></textarea>
        </div>
    </form>
    
</body>
</html>