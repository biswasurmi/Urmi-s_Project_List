<?php
// Start the session
session_start();
$isLoggedIn = isset($_SESSION["username"]) && isset($_SESSION["password"]);
// Connect to your database and fetch all rows from the database
// Replace 'your_database', 'your_username', 'your_password', and 'your_table' with your actual database credentials and table name.
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
// Fetch the post ID based on the session username (name attribute)
$post_id = 0; // Initialize the post ID to 0
$post_ids = array(); // Initialize an empty array to store post IDs
if (isset($_SESSION['username'])) {
    $session_username = $_SESSION['username'];

    // Prepare and execute a SELECT statement to get all post IDs based on the session username
    $sql = "SELECT id FROM post WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If post IDs are found, fetch and store them in the array
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $post_ids[] = $row['id'];
        }
    }

    $stmt->close();
}

// Fetch all applicants' names for each post ID and store them in a multidimensional array
$applicants_data = array(); // Initialize an empty array to store applicants' data
foreach ($post_ids as $post_id) {
    $applicants_names = array(); // Initialize an empty array to store applicants' names

    // Prepare and execute a SELECT statement to get applicants' names based on the post ID
    $sql = "SELECT post_applicant FROM applicants WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If applicants are found, fetch their names and store them in the array
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $applicants_names[] = $row['post_applicant'];
        }
    }

    // Close the prepared statement for applicants' names retrieval
    $stmt->close();

    // Fetch post details based on the post ID
    $post_sql = "SELECT * FROM post WHERE id = ?";
    $post_stmt = $conn->prepare($post_sql);
    $post_stmt->bind_param("i", $post_id);
    $post_stmt->execute();
    $post_result = $post_stmt->get_result();
    $post_row = $post_result->fetch_assoc();

    // Close the prepared statement for post details retrieval
    $post_stmt->close();

    // Store the applicants' names and post details in the multidimensional array
    $applicants_data[] = array(
        'post_id' => $post_id,
        'applicants_names' => $applicants_names,
        'post_title' => $post_row['title'],
        'post_category' => $post_row['category'],
    );
}

// Handle filtering by category
$filter_category = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["category"])) {
    $filter_category = $_GET["category"];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants_notification</title>
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
                            <th>Applicants</th>
                            <th>See profile</th>
                        </thead>
                        <tbody>
                            <?php
                                // Loop through each post ID and applicants data and display them in the table
                                if (count($applicants_data) > 0) {
                                    $count = 1;
                                    foreach ($applicants_data as $data) {
                                        // Check if the category matches the filter (if set), or show all posts if no filter is applied
                                        if ($filter_category === '' || $data['post_category'] === $filter_category) {
                                            // Display the first applicant in the same row as post information
                                            if (count($data['applicants_names']) > 0) {
                                                $first_applicant = $data['applicants_names'][0];
                                                echo "<tr>";
                                                echo "<td class='id'>" . $count . "</td>";
                                                echo "<td>" . $data['post_id'] . "</td>";
                                                echo "<td>" . $data['post_title'] . "</td>";
                                                echo "<td>" . $data['post_category'] . "</td>";
                                                echo "<td>" . $first_applicant . "</td>";
                                                echo "<td class='delete'><a href='show_user.php?username=" . urlencode($first_applicant) . "&post_id=" . $data['post_id'] . "'><i class='fas fa-arrow-right'></i></a></td>";
                                                echo "</tr>";

                                                // Display subsequent applicants in separate rows
                                                for ($i = 1; $i < count($data['applicants_names']); $i++) {
                                                    $applicant_name = $data['applicants_names'][$i];
                                                    echo "<tr>";
                                                    echo "<td></td>";
                                                    echo "<td></td>";
                                                    echo "<td></td>";
                                                    echo "<td></td>";
                                                    echo "<td>" . $applicant_name . "</td>";
                                                    echo "<td class='delete'><a href='show_user.php?username=" . urlencode($applicant_name) . "&post_id=" . $data['post_id'] . "'><i class='fas fa-arrow-right'></i></a></td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                // If no applicants, display "No applicants found" in the row
                                                echo "<tr>";
                                                echo "<td class='id'>" . $count . "</td>";
                                                echo "<td>" . $data['post_id'] . "</td>";
                                                echo "<td>" . $data['post_title'] . "</td>";
                                                echo "<td>" . $data['post_category'] . "</td>";
                                                echo "<td colspan='2'>No applicants found</td>";
                                                echo "</tr>";
                                            }

                                            $count++;
                                        }
                                    }
                                    if ($count === 1) {
                                        echo "<tr><td colspan='6'>No posts found for the selected category</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No posts found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <a class="add-new" href="add_post.php">Add Post</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
