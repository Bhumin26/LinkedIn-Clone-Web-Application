<?php
header('Content-Type: application/json');

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];


$conn = new mysqli("localhost:3306", "root", "", "test");


if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error));
    exit();
} else {

    $stmt = $conn->prepare("SELECT email FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(array("status" => "error", "message" => "Email already registered."));
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();


    $hashed_password = password_hash($password, PASSWORD_BCRYPT);


    $stmt = $conn->prepare("INSERT INTO registration (fullname, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(array("status" => "error", "message" => "Prepare statement failed: " . $conn->error));
        $conn->close();
        exit();
    }


    $stmt->bind_param("sss", $fullname, $email, $hashed_password);
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Registration failed: " . $stmt->error));
    }


    $stmt->close();
    $conn->close();
}
?>