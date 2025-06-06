<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID khách hàng không hợp lệ!']);
    exit;
  }

  // Lấy id_phong_tro của khách này
  $stmt = $conn->prepare("SELECT id_phong_tro FROM khach_hang WHERE id_khach=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($id_phong_tro);
  $stmt->fetch();
  $stmt->close();

  if ($id_phong_tro) {
    // Cập nhật trạng thái phòng về "Trống" và ngày hết hạn "NULL"
    $stmt = $conn->prepare("UPDATE phong_tro SET trang_thai='Trống', ngay_het_han_hop_dong=NULL WHERE id_phong_tro=?");
    $stmt->bind_param("i", $id_phong_tro);
    if (!$stmt->execute()) {
      echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật phòng: ' . $conn->error]);
      $stmt->close();
      $conn->close();
      exit;
    }
    $stmt->close();

    // Lấy id_nha_tro của phòng vừa cập nhật
    $stmt = $conn->prepare("SELECT id_nha_tro FROM phong_tro WHERE id_phong_tro=?");
    $stmt->bind_param("i", $id_phong_tro);
    $stmt->execute();
    $stmt->bind_result($id_nha_tro);
    $stmt->fetch();
    $stmt->close();

    // Cập nhật lại số phòng còn trống cho nhà trọ
    if ($id_nha_tro) {
        $conn->query("UPDATE nha_tro 
            SET phong_con_trong = (SELECT COUNT(*) FROM phong_tro WHERE id_nha_tro = $id_nha_tro AND trang_thai = 'Trống')
            WHERE id_nha_tro = $id_nha_tro");
    }
  }

  // Xóa khách hàng
  $stmt = $conn->prepare("DELETE FROM khach_hang WHERE id_khach=?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa khách hàng: ' . $conn->error]);
  }
  $stmt->close();
  $conn->close();
}
?>