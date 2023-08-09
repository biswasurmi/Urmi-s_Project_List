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
    }
    // Check if the post_id is provided in the URL parameter
    if (isset($_GET["post_id"])) {
        $post_id = $_GET["post_id"];

        // Fetch the author's details from the database based on the post ID
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

        $sql = "SELECT * FROM post WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $author_name = $row['actual_name'];
            $author_title = $row['title'];
            $author_post_description = $row['post_description'];
            $author_category = $row['category'];
        
            // Fetch author's email from the user table based on the post's author username
            $post_author_username = $row['name']; // Replace with the actual column name
            $user_query = $conn->prepare("SELECT email FROM user WHERE username = ?");
            $user_query->bind_param("s", $post_author_username);
            $user_query->execute();
            $user_result = $user_query->get_result();
        
            if ($user_result->num_rows === 1) {
                $user_row = $user_result->fetch_assoc();
                $author_email = $user_row['email'];
            } else {
                // User not found, handle the error
            }
        
            $user_query->close();
        } else {
            // Post not found, handle the error or redirect to another page
        }
        $stmt->close();
        $conn->close();
    }

    $applicationSuccess = false; // Variable to track application success
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["applyBtn"])) {
        // Check if the required session data exists (only the applicant's name is needed)
        if (isset($_SESSION['username'])) {
            // Get the applicant's name from the session
            $applicant_name = $_SESSION['username'];
    
            // Connect to the database and check if the applicant has already applied for the same post
            $conn = new mysqli($servername, $username, $password, $dbname);
    
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
    
            // Prepare and execute a SELECT statement to check if the same applicant has already applied for the same post
            $sql = "SELECT * FROM applicants WHERE post_id = ? AND post_applicant = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $post_id, $applicant_name);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                // The applicant has already applied for the same post
                // You can display a message or take any other action here
                $applicationSuccess = false;
                // Optionally, you can redirect the user back to the same page with an error message
                // header("Location: author_profile.php?post_id=" . $post_id . "&error=You have already applied for this post.");
                // exit;
            } else {
                // The applicant has not applied before, insert a new row into the applicants table
                $sql = "INSERT INTO applicants (post_id, post_applicant, post_author, post_title, post_desc, post_category) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssss", $post_id, $applicant_name, $author_name, $author_title, $author_post_description, $author_category);
                $stmt->execute();
                $stmt->close();
    
                $applicationSuccess = true; // Set the application success flag to true
            }
    
            // Close the database connection
            $conn->close();
        } else {
            // Redirect to the home page or any other desired page if the required session data is missing
            header("Location: home.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showprofile</title>
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
            margin-top: 10px;
            font-weight: bold;
            color: #8f1cb1;
            padding: 10px;
        }

        .main_body label {
            display: block;
            margin-top: 10px;
        }

        .main_body div[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #8f1cb1;
            border-radius: 3px;
            margin-bottom: 10px;
            color: black;
        }

        .main_body textarea {
            width: 100%;
            height: 100px;
            padding: 8px;
            border: 1px solid #8f1cb1;
            border-radius: 3px;
            margin-bottom: 10px;
            color: black;
        }

        .main_body button[type="submit"] {
            background-color: #34495e;
            color: #fff;
            padding: 10px;
            border: none;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .main_body button[type="submit"]:hover {
            background-color: #2e66a2;
        }
        .apply-btn {
            background-color: #34495e;
            color: #fff;
            padding: 10px;
            border: none;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
            align-items: center;
        }

        .apply-btn:hover {
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
        <div class="main_body">
            <label for="name">Name:</label>
            <div type="text" id="name"><?php echo isset($author_name) ? $author_name : 'Author name'; ?></div>

            <label for="email">Email:</label>
            <div type="text" id="email"><?php echo isset($author_email) ? $author_email : 'Author email'; ?></div>

            <label for="title">Title:</label>
            <div type="text" id="title"><?php echo isset($author_title) ? $author_title : 'Post title'; ?></div>

            <label for="category">Category:</label>
            <div type="text" id="category"><?php echo isset($author_category) ? $author_category : 'Post category'; ?></div>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo isset($author_post_description) ? $author_post_description : 'Post description'; ?></textarea>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?post_id=' . $post_id; ?>" method="POST">
            <button class="apply-btn" type="submit" name="applyBtn">APPLY</button>
        </form>
    <div>
    <?php if ($applicationSuccess): ?>
        <p style="color: green;">Application successful! You have applied for the post.</p>
    <?php elseif (isset($_POST["applyBtn"]) && !$applicationSuccess): ?>
        <p style="color: red;">You have already applied for this post.</p>
    <?php endif; ?>
</div>
    
</body>
</html>