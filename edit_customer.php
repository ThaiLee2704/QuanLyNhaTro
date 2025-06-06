<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $name = $_POST['name'];
  $gender = $_POST['gender'];
  $dob = $_POST['dob'] ?? null;
  $phone = $_POST['phone'];
  $email = $_POST['email'] ?? null;
  $cccd = $_POST['cccd'];
  $address = $_POST['address'] ?? null;
  $house = !empty($_POST['house']) ? intval($_POST['house']) : null;
  $room = !empty($_POST['room']) ? intval($_POST['room']) : null;

  // Lấy phòng cũ và ngày hết hạn cũ của khách hàng
  $stmt = $conn->prepare("SELECT id_phong_tro, id_nha_tro FROM khach_hang WHERE id_khach=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($old_room, $old_house);
  $stmt->fetch();
  $stmt->close();

  $contract_end_date = null;
  if ($old_room) {
    // Lấy ngày hết hạn hợp đồng của phòng cũ
    $stmt = $conn->prepare("SELECT ngay_het_han_hop_dong FROM phong_tro WHERE id_phong_tro=?");
    $stmt->bind_param("i", $old_room);
    $stmt->execute();
    $stmt->bind_result($contract_end_date);
    $stmt->fetch();
    $stmt->close();
  }

  // Nếu đổi phòng
  if ($old_room && $room && $old_room != $room) {
    // Cập nhật phòng cũ thành Trống, ngày hết hạn NULL
    $stmt = $conn->prepare("UPDATE phong_tro SET trang_thai='Trống', ngay_het_han_hop_dong=NULL WHERE id_phong_tro=?");
    $stmt->bind_param("i", $old_room);
    $stmt->execute();
    $stmt->close();

    // Cập nhật phòng mới thành Đã thuê, ngày hết hạn là ngày cũ
    $stmt = $conn->prepare("UPDATE phong_tro SET trang_thai='Đã thuê', ngay_het_han_hop_dong=? WHERE id_phong_tro=?");
    $stmt->bind_param("si", $contract_end_date, $room);
    $stmt->execute();
    $stmt->close();
  }

  // Xử lý ảnh mới (nếu có)
  $photo = null;
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $fileName = time() . '_' . basename($_FILES["photo"]["name"]);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes)) {
      if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $photo = $targetFile;
      }
    }
  }

  if ($photo) {
    $stmt = $conn->prepare("UPDATE khach_hang SET ho_ten=?, gioi_tinh=?, ngay_sinh=?, sdt=?, email=?, cccd=?, dia_chi_thuong_tru=?, id_nha_tro=?, id_phong_tro=?, anh=? WHERE id_khach=?");
    $stmt->bind_param(
      'ssssssssssi',
      $name,
      $gender,
      $dob,
      $phone,
      $email,
      $cccd,
      $address,
      $house,
      $room,
      $photo,
      $id
    );
  } else {
    $stmt = $conn->prepare("UPDATE khach_hang SET ho_ten=?, gioi_tinh=?, ngay_sinh=?, sdt=?, email=?, cccd=?, dia_chi_thuong_tru=?, id_nha_tro=?, id_phong_tro=? WHERE id_khach=?");
    $stmt->bind_param(
      'sssssssssi',
      $name,
      $gender,
      $dob,
      $phone,
      $email,
      $cccd,
      $address,
      $house,
      $room,
      $id
    );
  }

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }

  $stmt->close();
  $conn->close();
}
?>