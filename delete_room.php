<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $room_id = $_POST['room_id'];
  $stmt = $conn->prepare("DELETE FROM phong_tro WHERE id_phong_tro=?");
  $stmt->bind_param("i", $room_id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>