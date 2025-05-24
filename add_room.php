<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_nha_tro = $_POST['id_nha_tro'];
  $area = $_POST['area'];
  $price = $_POST['price'];
  $max_people = $_POST['max_people'];
  $status = $_POST['status'];
  $facilities = $_POST['facilities'];
  $contract_end_date = $_POST['contract_end_date'] ?: null;

  $stmt = $conn->prepare("INSERT INTO phong_tro (id_nha_tro, dien_tich, gia_thue, so_nguoi_toi_da, trang_thai, co_so_vat_chat, ngay_het_han_hop_dong) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iddssss", $id_nha_tro, $area, $price, $max_people, $status, $facilities, $contract_end_date);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>