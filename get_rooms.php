<?php
include 'db.php';

$id_nha_tro = $_GET['id_nha_tro'] ?? null;

if ($id_nha_tro) {
  $stmt = $conn->prepare("SELECT id_phong_tro FROM phong_tro WHERE id_nha_tro = ? AND trang_thai = 'Trống'");
  $stmt->bind_param('i', $id_nha_tro);
  $stmt->execute();
  $result = $stmt->get_result();

  $rooms = [];
  while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
  }

  echo json_encode($rooms);
  $stmt->close();
}

$conn->close();
?>