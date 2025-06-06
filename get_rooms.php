<?php
include 'db.php';

$id_nha_tro = $_GET['id_nha_tro'] ?? null;
$id_phong_hien_tai = $_GET['id_phong_hien_tai'] ?? null;

if ($id_nha_tro) {
  if ($id_phong_hien_tai) {
    // Lấy phòng trống hoặc phòng hiện tại của khách
    $stmt = $conn->prepare("SELECT id_phong_tro FROM phong_tro WHERE id_nha_tro = ? AND (trang_thai = 'Trống' OR id_phong_tro = ?)");
    $stmt->bind_param('ii', $id_nha_tro, $id_phong_hien_tai);
  } else {
    // Chỉ lấy phòng trống
    $stmt = $conn->prepare("SELECT id_phong_tro FROM phong_tro WHERE id_nha_tro = ? AND trang_thai = 'Trống'");
    $stmt->bind_param('i', $id_nha_tro);
  }
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