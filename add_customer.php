<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Start transaction
  $conn->begin_transaction();
  try {
    $name = $_POST['name'];
    $gender = $_POST['gender']; 
    $dob = $_POST['dob'] ?? null;
    $phone = $_POST['phone'];
    $email = $_POST['email'] ?? null;
    $cccd = $_POST['cccd'];
    $address = $_POST['address'] ?? null;
    $house = !empty($_POST['house']) ? intval($_POST['house']) : null;
    $room = !empty($_POST['room']) ? intval($_POST['room']) : null;
    $contract_end_date = $_POST['contract_end_date'] ?? null;

    // Xử lý ảnh upload
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

    // Kiểm tra dữ liệu đầu vào
    if (empty($name) || empty($gender) || empty($dob) || empty($phone) || empty($cccd) || empty($contract_end_date)) {
      throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc.');
    }

    // Kiểm tra phòng đã có người thuê chưa
    if ($room) {
      $stmt = $conn->prepare("SELECT trang_thai FROM phong_tro WHERE id_phong_tro = ?");
      $stmt->bind_param("i", $room);
      $stmt->execute();
      $result = $stmt->get_result();
      $room_status = $result->fetch_assoc();
      
      if ($room_status && $room_status['trang_thai'] === 'Đã thuê') {
        throw new Exception('Phòng này đã có người thuê!');
      }
    }

    // Thêm khách hàng
    $stmt = $conn->prepare("INSERT INTO khach_hang (ho_ten, gioi_tinh, ngay_sinh, sdt, email, cccd, dia_chi_thuong_tru, id_nha_tro, id_phong_tro, anh) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssssss', $name, $gender, $dob, $phone, $email, $cccd, $address, $house, $room, $photo);

    if (!$stmt->execute()) {
      throw new Exception($conn->error);
    }

    // Cập nhật trạng thái phòng và ngày hết hạn hợp đồng
    if ($room) {
      $stmt = $conn->prepare("UPDATE phong_tro SET trang_thai = 'Đã thuê', ngay_het_han_hop_dong = ? WHERE id_phong_tro = ?");
      $stmt->bind_param("si", $contract_end_date, $room);
      if (!$stmt->execute()) {
        throw new Exception('Không thể cập nhật trạng thái phòng.');
      }
    }

    // Cập nhật lại số phòng cho nhà trọ
    $id_nha_tro = (int)$house;
    $conn->query("UPDATE nha_tro 
      SET tong_so_phong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro),
          phong_con_trong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro AND trang_thai = 'Trống')
      WHERE id_nha_tro = $id_nha_tro");

    // Commit nếu mọi thứ OK
    $conn->commit();
    echo json_encode(['success' => true]);

  } catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }

  $stmt->close();
  $conn->close();
}
?>