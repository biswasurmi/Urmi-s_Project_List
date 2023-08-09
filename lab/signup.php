<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["signupBtn"])) {
        // Process signup request
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $role = $_POST["role"]; // Make sure this is set properly in your form

        // Validate username, email, and password fields
        if (empty($username) || empty($role) || empty($email) || empty($password)) {
            echo "<script>alert('Please fill in all fields.');</script>";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
        } else if (strlen($password) < 6) {
            echo "<script>alert('Password must be at least 6 characters long.');</script>";
        } else {
            // Check if the user with the provided username already exists in the database
            $query = "SELECT * FROM user WHERE email = ? OR username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User already exists, show an error message or handle as needed
                echo "<script>alert('Username/Email already exists. Please choose a different username/email.');</script>";
            } else {
                $insertQuery = "INSERT INTO user (role, username, name, email, password) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("sssss", $role, $username, $username, $email, $password);
                if ($insertStmt->execute()) {
                    // User data inserted successfully, redirect to update_profile.php
                    session_start();
                    $_SESSION["username"] = $username;
                    $_SESSION["name"] = $username;
                    $_SESSION["password"] = $password;
                    $_SESSION["email"] = $email;
                    $_SESSION["role"] = $role;
                    header("Location: update_profile.php");
                    exit;
                } else {
                    // Error inserting user data, show an error message or handle as needed
                    echo "<script>alert('Error creating user. Please try again.');</script>";
                }
            }
            $stmt->close();
        }
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign_up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            height: 100vh;
            background-image: linear-gradient(rgba(0, 0, 50, 0.8), rgba(0, 0, 50, 0.8)), url(image.jpeg);
            background-position: center;
            background-size: cover;
            position: relative;
        }

        .form-box {
            width: 90%;
            max-width: 450px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 50px 60px 70px;
            text-align: center;
        }

        .form-box h1 {
            font-size: 30px;
            margin-bottom: 60px;
            color: #3c00a0;
            position: relative;
        }

        .form-box h1::after {
            content: '';
            width: 30px;
            height: 4px;
            border-radius: 3px;
            background: #3c00a0;
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translate(-50%);
        }

        .input-field {
            background: #eaeaea;
            margin: 15px 0;
            border-radius: 3px;
            display: flex;
            align-items: center;
            max-height: 65px;
            transition: max-height 0.5s;
            overflow: hidden;
        }

        .input-field i {
            margin-left: 15px;
            color: #999;
        }

        input,
        select {
            width: 100%;
            background: transparent;
            border: 0;
            outline: 0;
            padding: 18px 15px;
        }

        form p {
            text-align: left;
            font-size: 13px;
        }

        form p a {
            text-decoration: none;
            color: #3c00a0;
        }

        .btn-field {
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .btn-field button {
            flex-basis: 48%;
            background: #3c00a0;
            color: #fff;
            height: 40px;
            border-radius: 20px;
            border: 0;
            outline: 0;
            cursor: pointer;
            transition: background 1s;
        }

        .btn-field button.disable {
            background: #eaeaea;
            color: #555;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h1 id="title">Sign Up</h1>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="input-group">
                    <div class="input-field" id="namefield">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username" placeholder="Name">
                    </div>
                    <div class="input-field">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email">
                    </div>
                    <div class="input-field">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Password">
                    </div>
                    <div class="input-field">
                        <i class="fa-solid fa-users"></i>
                        <select name="role">
                            <option value="student">Student</option>
                            <option value="recruiter">Recruiter</option>
                        </select>
                    </div>
                </div>

                <div class="btn-field">
                    <button type="submit" name="signupBtn">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>