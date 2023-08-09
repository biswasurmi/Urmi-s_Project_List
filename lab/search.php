<?php
// Start the session
session_start();

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

    // Check if 'username' key is set in the session before using it
    if (isset($_SESSION['username'])) {
        $sessionEmail = $_SESSION['email'];
        $query = "SELECT role, description FROM user WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $sessionEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userRole = $row["role"];
        }
        

    }
    if (isset($_SESSION['username'])) {
        $sql = "SELECT * FROM post WHERE name != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $result = $stmt->get_result();

            // Handle filtering by category
        $filter_category = '';
        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["category"])) {
            $filter_category = $_GET["category"];
        }
    }
    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - All Posts</title>
    <!-- Include the Font Awesome CSS link here -->
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        #admin-content {
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-10,
        .col-md-2,
        .col-md-12 {
            padding: 10px;
        }

        .admin-heading {
            font-size: 24px;
            font-weight: bold;
            color: #8f1cb1;
        }

        .add-new {
            background-color: #8f1cb1;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table th,
        .content-table td {
            padding: 12px;
            text-align: left;
        }

        .content-table th {
            background-color: #8f1cb1;
            color: white;
        }

        .content-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .delete button {
            background-color: #8f1cb1; /* Red color for the background */
            color: #fff; /* White color for the text */
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Hover effect for the delete button */
        .delete button:hover {
            background-color: #327ba8; /* Darker shade of red on hover */
        }
        /* Responsive Styles */
        @media (max-width: 768px) {
            .col-md-10 {
                width: 100%;
            }

            .col-md-2 {
                width: 100%;
                text-align: center;
            }

            .col-md-12 {
                width: 100%;
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
                <div class="col-md-10">
                    <h1 class="admin-heading">All Posts</h1>

                    <!-- Add the filter form here -->
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                        <label for="category">Filter by Category:</label>
                        <select name="category" id="category">
                            <option value="">ALL</option>
                            <option value="Programming">Programming</option>
                            <option value="Internship">Internship</option>
                            <option value="Research">Research</option>
                            <option value="Web development">Web development</option>
                        </select>
                        <button type="submit">Filter</button>
                    </form>
                    <!-- End of filter form -->

                    </div>
                <div class="col-md-12">
                    <table class="content-table">
                        <thead>
                            <th>S.No.</th>
                            <th>Post_id</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>SEE MORE</th>
                        </thead>
                        <tbody>
                        <?php
                            
                            if ($result->num_rows > 0) {
                                $count = 1;
                                while ($row = $result->fetch_assoc()) {
                                    // Check if the category matches the filter (if set), or show all posts if no filter is applied
                                    if ($filter_category === '' || $row['category'] === $filter_category) {
                                        echo "<tr>";
                                        echo "<td class='id'>" . $count . "</td>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['title'] . "</td>";
                                        echo "<td>" . $row['category'] . "</td>";
                                        echo "<td>" . $row['actual_name'] . "</td>";
                                        echo "<td class='delete'><a href='show_profile.php?post_id=" . $row['id'] . "'><button><i class='fas fa-arrow-right'></i></button></a></td>";

                                        echo "</tr>";

                                        $count++;
                                    }
                                }
                                if ($count === 1) {
                                    echo "<tr><td colspan='5'>No posts found for the selected category</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No posts found</td></tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>