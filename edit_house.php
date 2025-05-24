<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id_nha_tro'];
  $ten = $_POST['ten_nha_tro'];
  $diachi = $_POST['dia_chi_nha_tro'];
  $stmt = $conn->prepare("UPDATE nha_tro SET ten_nha_tro=?, dia_chi_nha_tro=? WHERE id_nha_tro=?");
  $stmt->bind_param("ssi", $ten, $diachi, $id);
  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>