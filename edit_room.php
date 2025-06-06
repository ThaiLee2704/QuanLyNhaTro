<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $room_id = $_POST['room_id'];
  $area = $_POST['area'];
  $price = $_POST['price'];
  $max_people = $_POST['max_people'];
  $status = $_POST['status'];
  $facilities = $_POST['facilities'];
  $contract_end_date = $_POST['contract_end_date'];

  // Lấy id_nha_tro của phòng này
  $stmt = $conn->prepare("SELECT id_nha_tro FROM phong_tro WHERE id_phong_tro=?");
  $stmt->bind_param("i", $room_id);
  $stmt->execute();
  $stmt->bind_result($id_nha_tro);
  $stmt->fetch();
  $stmt->close();

  if (!$id_nha_tro) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy nhà trọ cho phòng này!']);
    $conn->close();
    exit;
  }

  // Cập nhật phòng với đầy đủ trường
  $stmt = $conn->prepare("UPDATE phong_tro SET dien_tich=?, gia_thue=?, so_nguoi_toi_da=?, trang_thai=?, co_so_vat_chat=?, ngay_het_han_hop_dong=? WHERE id_phong_tro=?");
  $stmt->bind_param("diisssi", $area, $price, $max_people, $status, $facilities, $contract_end_date, $room_id);

  if ($stmt->execute()) {
    $id_nha_tro = (int)$id_nha_tro;
    $sql_update = "UPDATE nha_tro 
      SET tong_so_phong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro),
          phong_con_trong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro AND trang_thai = 'Trống')
      WHERE id_nha_tro = $id_nha_tro";
    if ($conn->query($sql_update)) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'message' => $conn->error]);
    }
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>