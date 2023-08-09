<?php
    $applicationSuccess = false;
    // Start the session
    session_start();

    // Check if the user is logged in (i.e., if the required session data exists)
    $isLoggedIn = isset($_SESSION["username"]) && isset($_SESSION["password"]);
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
    }
    // Check if the username is provided in the URL parameter
    if (isset($_GET["username"]) && isset($_GET["post_id"])) {
        $username = $_GET["username"];
        $post_id = $_GET["post_id"];

        // Connect to the database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "new_site";

        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute a SELECT statement to fetch user details based on the username
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // If user details are found, fetch and store them in variables
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $user_username = $row['username'];
            $user_email = $row['email'];
            $user_description = $row['description'];
        } else {
            // User not found, handle the error or redirect to another page
            // For example, redirecting to an error page:
            header("Location: error_page.php");
            exit;
        }
        if (isset($_POST["applyBtn"]) && $isLoggedIn) {
            // Check if the person has already been recruited for this post
            $checkRecruitmentSql = "SELECT * FROM recruiters WHERE post_id = ? AND post_applicants = ? AND post_author = ?";
            $checkStmt = $conn->prepare($checkRecruitmentSql);
            $checkStmt->bind_param("iss", $post_id, $username, $_SESSION["username"]);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
        
            if ($checkResult->num_rows > 0) {
                // The person has already been recruited for this post
                $applicationSuccess = false;
            } else {
                // Prepare and execute an INSERT statement to insert data into the recruiters table
                $insertSql = "INSERT INTO recruiters (post_id, post_applicants, post_author) VALUES (?, ?, ?)";
                $stmtInsert = $conn->prepare($insertSql);
                $stmtInsert->bind_param("iss", $post_id, $username, $_SESSION["username"]);
        
                // Check if the insertion was successful
                if ($stmtInsert->execute()) {
                    $applicationSuccess = true;
                    // Prepare and execute a DELETE statement to remove data from the applicants table
                    $deleteSql = "DELETE FROM applicants WHERE post_id = ? AND post_applicant = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("is", $post_id, $username);
        
                    // Check if the deletion was successful
                    if ($deleteStmt->execute()) {
                        //echo "Applicant's information removed successfully";
                    } else {
                        // Handle the error (e.g., display an error message)
                    }
                } else {
                    $applicationSuccess = false;
                }
        
                // Close the prepared statement for insertion
                $stmtInsert->close();
                // Close the prepared statement for deletion
                $deleteStmt->close();
            }
        
            // Close the prepared statement for checking recruitment
            $checkStmt->close();
        }
        // Close the prepared statement for user details retrieval
        $stmt->close();

        // Close the database connection
        $conn->close();
    } else {
        // Redirect to another page if the username is not provided in the URL
        header("Location: error_page.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showuser</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Navigation Bar Styles */
        .nav-bar {
            background-color:  #34495e;
            padding: 10px 0;
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
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

        .logout-btn {
            margin-left: auto;
            padding: 10px 20px;
            background-color: #34495e;
            border: none;
            color: #f1ecec;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #2e66a2;
        }

        .dashboard-btn {
            display: none;
        }

        @media screen and (max-width: 600px) {
            .nav-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-bar a {
                margin: 5px 0;
            }

            .dashboard-btn {
                display: block;
                margin: 5px 0;
            }
        }
        
        .recruit-btn {
            background-color: #34495e;
            color: #fff;
            padding: 10px;
            border: none;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .recruit-btn:hover {
            background-color: #2e66a2;
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
    <div>
        <h1 style="color: #8f1cb1">Applicants Profile</h1>
        <h2><p style="color: #8f1cb1">Author Name: </p></h2><h3><p><?php echo isset($user_username) ? $user_username : "Not Found"; ?></p></h3>
        <h2><p style="color: #8f1cb1">Email: </p></h2><h3><p><?php echo isset($user_email) ? $user_email : "Not Found"; ?></p></h3>
        <h2><p style="color: #8f1cb1">Description: </p></h2><h3><p><?php echo isset($user_description) ? $user_description : "Not Found"; ?></p></h3>
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?username=' . urlencode($username) . '&post_id=' . urlencode($post_id); ?>" method="POST">
            <!-- Add a hidden input field to pass the post_id -->
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <button class="recruit-btn" type="submit" name="applyBtn">Recruit</button>
        </form>
        <div>
            <?php if ($applicationSuccess): ?>
                <p style="color: green;">Applicants will get the notification</p>
            <?php elseif (isset($_POST["applyBtn"]) && !$applicationSuccess): ?>
                <p style="color: red;">You have recruited the person before and he will have the notification</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>