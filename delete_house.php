<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id_nha_tro'];

  // Xóa tất cả phòng trọ thuộc nhà trọ này trước
  $conn->query("DELETE FROM phong_tro WHERE id_nha_tro = " . intval($id));

  $stmt = $conn->prepare("DELETE FROM nha_tro WHERE id_nha_tro=?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>