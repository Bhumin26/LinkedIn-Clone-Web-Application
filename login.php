<?php
$email = $_POST['email'];
$password = $_POST['password'];

$conn = new mysqli("localhost:3306", "root", "", "test");

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
} else {

    $stmt = $conn->prepare("SELECT password FROM registration WHERE email = ?");


    $stmt->bind_param("s", $email);
    $stmt->execute();


    $stmt->store_result();
    $stmt->bind_result($hashed_password);


    if ($stmt->fetch()) {

        if (password_verify($password, $hashed_password)) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Login Failed: Password mismatch"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Login Failed: User not found"));
    }


    $stmt->close();
    $conn->close();
}
?>