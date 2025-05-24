<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $stmt = $conn->prepare("DELETE FROM khach_hang WHERE id_khach=?");
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