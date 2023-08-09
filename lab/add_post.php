<?php
    // Start the session
    session_start();

    // Check if the user is logged in (i.e., if the required session data exists)
    $isLoggedIn = isset($_SESSION["username"]) && isset($_SESSION["password"]);
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

    // Database connection details
    $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "new_site";

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        // Get the form data
        $post_title = $_POST["post_title"];
        $postdesc = $_POST["postdesc"];
        $category = $_POST["category"];
        $username = $_SESSION['username'];
        $newname = $_SESSION['name'];

        // Prepare and execute the SQL query to insert the data into the database
        $stmt = $conn->prepare("INSERT INTO post (name, actual_name, title, post_description, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $newname, $post_title, $postdesc, $category);
        $stmt->execute();
    
        // Close the prepared statement
        $stmt->close();
    
        // Redirect to the "All Posts" page after successful submission
        header("Location: see_post.php");
        exit;
    }

    // Close the database connection
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        * {
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
            border: border-box;
        }
        

        /*add post section */
        body {
            font-family: Arial, sans-serif;
        }
        #admin-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-heading {
            font-size: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8f1cb1;
        }
        .form-group {
            margin-bottom: 20px;
            color: #8f1cb1;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color:#8f1cb1;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #8f1cb1;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            
        }
        input[type="file"] {
            padding: 10px;
        }
        .btn-primary {
            background-color: #34495e;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary:hover {
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
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="admin-heading">ADD NEW POST</h1>
                </div>
                <div class="col-md-offset-3 col-md-6">
                    <!-- Form -->
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="post_title">Title</label>
                            <input type="text" name="post_title" id="post_title" required>
                        </div>
                        <div class="form-group">
                            <label for="postdesc">DESCRIPTION</label>
                            <textarea name="postdesc" id="postdesc" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category">CATEGORY</label>
                            <select name="category" id="category" required>
                                <option value="" selected>Select Category</option>
                                <option value="Internship">Internship</option>
                                <option value="Research">Research</option>
                                <option value="Programming">Programming</option>
                                <option value="Web development">Web development</option>
                            </select>
                        </div>
                        
                        <input type="submit" name="submit" class="btn btn-primary" value="SAVE">
                    </form>
                    <!--/Form -->
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>
