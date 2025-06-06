<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $room_id = $_POST['room_id'];

  // Lấy id_nha_tro của phòng này
  $stmt = $conn->prepare("SELECT id_nha_tro FROM phong_tro WHERE id_phong_tro=?");
  $stmt->bind_param("i", $room_id);
  $stmt->execute();
  $stmt->bind_result($id_nha_tro);
  $stmt->fetch();
  $stmt->close();

  // Xóa phòng
  $stmt = $conn->prepare("DELETE FROM phong_tro WHERE id_phong_tro=?");
  $stmt->bind_param("i", $room_id);

  if ($stmt->execute()) {
    // Cập nhật lại số phòng cho nhà trọ
    $conn->query("UPDATE nha_tro 
      SET tong_so_phong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro),
          phong_con_trong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro AND trang_thai = 'Trống')
      WHERE id_nha_tro = $id_nha_tro");
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>