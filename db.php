<?php
// Thông tin kết nối cơ sở dữ liệu
$host = 'localhost'; // Địa chỉ server (thường là localhost)
$username = 'root'; // Tên người dùng MySQL (mặc định là root)
$password = ''; // Mật khẩu MySQL (mặc định là rỗng)
$database = 'quanlynhatro'; // Tên cơ sở dữ liệu của bạn

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>