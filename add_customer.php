<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $gender = $_POST['gender'];
  $dob = $_POST['dob'] ?? null;
  $phone = $_POST['phone'];
  $email = $_POST['email'] ?? null;
  $cccd = $_POST['cccd'];
  $address = $_POST['address'] ?? null;
  $house = $_POST['house'] ?? null;
  $room = $_POST['room'] ?? null;

  // Kiểm tra dữ liệu đầu vào
  if (empty($name) || empty($gender) || empty($phone) || empty($cccd)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.']);
    exit;
  }

  // Thêm khách hàng vào cơ sở dữ liệu
  $stmt = $conn->prepare("INSERT INTO khach_hang (ho_ten, gioi_tinh, ngay_sinh, sdt, email, cccd, dia_chi_thuong_tru, id_nha_tro, id_phong_tro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('sssssssss', $name, $gender, $dob, $phone, $email, $cccd, $address, $house, $room);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Không thể thêm khách hàng.']);
  }

  $stmt->close();
  $conn->close();
}
?>