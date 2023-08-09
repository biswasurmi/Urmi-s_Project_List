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
    <title>Home</title>
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
            font-family: 'Times New Roman', Times, serif;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        header {
            background-color: #e0ecec;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        .up {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
        }

        .up a {
            color: #b73434;
            text-decoration: none;
            font-size: small;
        }

        .moto {
            padding: 15px;
            text-align: center;
            background-color: white;
            color: #8f1cb1;
        }

        #b1 {
            color: #8f1cb1;
            font-weight: 500;
            font-size: 24px;
            text-align: center;
        }

        /* Blog Section */
        .blog {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .box {
            width: 100%;
            max-width: 300px;
            margin: 10px;
            padding: 10px;
            background-color: #120808;
            border-radius: 50px;
        }

        /* Featured Jobs Section */
        .job {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            background-color: white;
            padding: 20px 0;
        }

        .bo {
            width: 100%;
            max-width: 200px;
            height: 200px;
            margin: 10px;
            
            color:#8f1cb1;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .box-img {
            height: 150px;
            width: 100%;
            background-size: cover;
            background-position: center;
            margin-bottom: 10px;
            border-radius: 50px;
        }

        /* Responsive Styles */
        @media screen and (max-width: 1200px) {
            .box {
                max-width: 100%;
            }
            
        }

        @media screen and (max-width: 800px) {
            .up {
                flex-direction: column;
            }

            #abc1 {
                padding: 2px;
                margin: 2px;
            }

            .blog, .job {
                flex-direction: column;
            }
            
        }
        @media screen and (max-width: 800px) {
            .job {
                flex-direction: column; /* Change to vertical layout on smaller screens */
                align-items: center; /* Center the job boxes vertically */
            }

            .bo {
                max-width: 100%; /* Make job boxes take full width on smaller screens */
            }
            
        }
    </style>
</head>
<body>
    <header>
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
    <br><br>
        <div class="moto">
            <h1 style="background color:white">FIND THE PERFECT SERVICES FOR YOUR<br>BUSINESS!!<br>FIND YOUR NEW JOB TODAY!<br></h1>
        </div>
        <br><br>
        <div id="b1">
            <p><i class="fa-brands fa-blogger"></i>BLOG</p>
        </div>
        <br>
        <div class="blog">
            <div class="box">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint odio labore earum, accusantium numquam, provident ratione tempora quam nobis aliquid iure voluptatum, suscipit enim dignissimos ipsam doloremque. Qui, tenetur pariatur.</p>
                <br>JOHN DOE<br><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <div class="box">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad quasi itaque, ipsam veritatis accusamus nobis sunt libero deleniti dolorum non modi? Exercitationem distinctio quasi unde blanditiis accusamus, atque laborum provident.</p>
                <br>JOHN DOE<br><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <div class="box">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam vel corporis id at veniam fugit deleniti, itaque nihil provident reprehenderit, quod doloremque quis pariatur tenetur repudiandae sequi iusto, quam velit?</p>
                <br>JOHN DOE<br><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
        </div>
        <br><br>
        <div id="b1">
            <p><i class="fa-solid fa-briefcase"></i>FEATURED JOBS</p>
        </div>
        <br>
        <div class="job">
            <div class="box1 bo">
                <h2>INTERNSHIP</h2>
                <div class="box-img" style="background-image: url('img1.jpeg');"></div>
            </div>
            <div class="box2 bo">
                <h2>RESEARCH</h2>
                <div class="box-img" style="background-image: url('lock1.jpg');"></div>
            </div>
            <div class="box3 bo">
                <h2>PROGRAMMING</h2>
                <div class="box-img" style="background-image: url('reg.webp');"></div>
            </div>
            <div class="box4 bo">
                <h2>WEB DEVELOPMENT</h2>
                <div class="box-img" style="background-image: url('login.jpg');"></div>
            </div>
        </div>
    </header>
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
