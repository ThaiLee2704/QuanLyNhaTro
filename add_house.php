<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ten = $_POST['ten_nha_tro'];
  $diachi = $_POST['dia_chi_nha_tro'];
  $stmt = $conn->prepare("INSERT INTO nha_tro (ten_nha_tro, dia_chi_nha_tro) VALUES (?, ?)");
  $stmt->bind_param("ss", $ten, $diachi);
  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>