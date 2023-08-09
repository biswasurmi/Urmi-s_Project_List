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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
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
            /* Footer Styles */
        footer {
            background-color: rgb(9, 6, 3);
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        
        .end {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            align-items: center;
        }
        
        .end1 {
            height: 50px;
            display: flex;
            align-items: center;
            flex-direction: column; /* Adjust flex direction for smaller screens */
        }
        
        .end1 i {
            font-size: 24px;
            margin-right: 5px;
        }
        
        .end2 i {
            font-size: 24px;
            margin: 0 5px;
        }
        
        .end2 {
            margin-top: 10px;
        }
        
        @media screen and (max-width: 600px) {
            .end {
                flex-direction: column;
            }
        
            .end1,
            .end2 {
                width: 100%;
                text-align: center;
            }
        
            .end1 p {
                font-size: 18px;
                margin-top: 5px; /* Add margin to separate brandname and icons */
                text-align: center;
            }
        
            .end2 {
                margin-top: 5px;
            }
        }
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                background-color: rgb(208, 224, 219);
                font-family: "Times New Roman", Times, serif;
            }
            
            .header {
                height: 100px;
                width: auto;
                background-color: white;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .header h1 {
                text-align: center;
                padding: 15px;
                color: #8f1cb1;
            }
            
            .content {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-evenly;
                background-color: white;
                padding: 50px 0;
            }
            
            .about-text {
                width: 40%;
                text-align: justify;
                color: black;
                padding: 0 20px;
                margin: 0; /* Remove margin */
            }
            
            .about-image {
                background-image: url('abo.jpg');
                background-size: cover;
                background-position: center;
                height: 300px;
                width: 50%;
                margin: 0; /* Remove margin */
            }
                        
            .spacer {
                height: 200px;
            }
            
            .job-content {
                display: flex;
                align-items: center;
                background-color: white;
                padding: 50px 0;
            }
            
            .job-image {
                background-image: url('abo1.jpg');
                background-size: cover;
                background-position: center;
                height: 300px;
                width: 50%;
            }
            
            .job-details {
                width: 50%;
                text-align: justify;
                color: #8f1cb1;
                padding: 0 20px;
            }
            
            /* Media Queries for Responsiveness */
            @media (max-width: 800px) {
                .content {
                    flex-direction: column;
                    align-items: center;
                }
            
                .about-image,
                .job-image {
                    width: 100%;
                    height: 200px;
                }
            
                .about-text,
                .job-details {
                    width: 80%;
                    margin: 20px 0;
                }
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
            <a href="update_profile.php">UPDATE</i></a>
            <a href="see_profile.php">PROFILE</i></a>
            <!-- Logout button is aligned to the right -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <button class="logout-btn" type="submit" name="logoutBtn">LOGOUT</button>
            </form>
        <?php else : ?>
            <a href="signin.php">SIGN IN</a>
            <a href="signup.php">SIGN UP</a>
        <?php endif; ?>
    </div>
    <div class="header">
        <h1>About Us</h1>
    </div>
    <div class="content">
        <div class="about-text">
            <h1>We've been working hard to help our customers.</h1>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Debitis neque harum cumque libero eum, sapiente necessitatibus, nam animi natus voluptate voluptatem quas temporibus voluptatum nobis iure. Quaerat nisi libero a!
            Omnis dolor voluptatem quos blanditiis tempore natus quod dolorem officiis. Dolore, ab nam. Aliquam totam ut perferendis placeat. Sint, porro! Recusandae facilis, ex eaque similique saepe laudantium deleniti at qui!</p>
        </div>
        <div class="about-image"></div>
    </div>
    <div class="job-content">
        <div class="job-image"></div>
        <div class="job-details">
            <h3><i class="fab fa-gg"></i> 24/7 Support</h3>
            <h3><i class="fab fa-squarespace"></i> Easy to Navigate</h3>
            <h3><i class="fab fa-buffer"></i> Free To Register</h3>
            <h3><i class="fab fa-dropbox"></i> Email System</h3>
        </div>
    </div>
    <footer>
        <div class="end">
            <div class="end1">
                <p style="text-decoration: underline; font-size: larger;">brandname</p>
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <div class="end2">
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-twitter"></i>
                <i class="fa-brands fa-youtube"></i>
            </div>
            <div>
                <i class="fa-solid fa-copyright"></i>
                <p>2023 all rights reserved</p>
            </div>
        </div>
    </footer>
</body>
</html>
