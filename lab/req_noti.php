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
    // Connect to your database and fetch all rows from the database
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
    $recruiters_data = array(); // Initialize an empty array to store recruiters' data
    if (isset($_SESSION['username'])) {
        $session_username = $_SESSION['username'];

        $sql = "SELECT * FROM recruiters WHERE post_applicants = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $session_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $author_username = $row['post_author'];
            
                // Fetch author's name and email based on the author's username
                $author_query = "SELECT name, email FROM user WHERE username = ?";
                $author_stmt = $conn->prepare($author_query);
                $author_stmt->bind_param("s", $author_username);
                $author_stmt->execute();
                $author_result = $author_stmt->get_result();
                $author_row = $author_result->fetch_assoc();

                $recruiters_data[] = array(
                    'post_id' => $row['post_id'],
                    'post_applicants' => $row['post_applicants'],
                    'post_author' => $author_row['name'], // Author's name from user table
                    'post_email' => $author_row['email'], // Author's email from user table
                );
            }
        }

        $stmt->close(); // Close the statement, not the connection
    }

    // Determine the category filter value
    $category_filter = isset($_GET['category']) ? $_GET['category'] : '';

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
                            <th>Author Name</th>
                            <th>Author Email</th>
                        </thead>
                        <tbody>
                            <?php
                            // Loop through each recruiter's data and display the required information in the table
                            $count = 1;
                            foreach ($recruiters_data as $data) {
                                // Fetch post details based on the post ID
                                $post_sql = "SELECT * FROM post WHERE id = ?";
                                $post_stmt = $conn->prepare($post_sql);
                                $post_stmt->bind_param("i", $data['post_id']);
                                $post_stmt->execute();
                                $post_result = $post_stmt->get_result();
                                $post_row = $post_result->fetch_assoc();

                                // Check if the post matches the category filter
                                $display_post = true;
                                if (!empty($category_filter)) {
                                    if ($post_row['category'] !== $category_filter) {
                                        $display_post = false;
                                    }
                                }

                                if ($display_post) {
                                    // Display the post details and author name
                                    echo "<tr>";
                                    echo "<td class='id'>" . $count . "</td>";
                                    echo "<td>" . $data['post_id'] . "</td>";
                                    echo "<td>" . $post_row['title'] . "</td>";
                                    echo "<td>" . $post_row['category'] . "</td>";
                                    echo "<td>" . $data['post_author'] . "</td>";
                                    echo "<td>" . $data['post_email'] . "</td>";
                                    
                                    echo "</tr>";
                                    $count++;
                                }

                                // Close the prepared statement for post details retrieval
                                $post_stmt->close();
                            }

                            if ($count === 1) {
                                echo "<tr><td colspan='6'>No posts found for the selected category</td></tr>";
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
