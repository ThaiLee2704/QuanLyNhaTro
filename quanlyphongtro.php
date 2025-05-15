<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Lấy ID nhà trọ từ URL
$id_nha_tro = $_GET['id'] ?? null;

if (!$id_nha_tro) {
    die("Không tìm thấy ID nhà trọ.");
}

// Truy vấn dữ liệu từ bảng phong_tro
$sql = "SELECT id_phong_tro AS room_id, dien_tich AS area, gia_thue AS price, 
               so_nguoi_toi_da AS max_people, trang_thai AS status, 
               co_so_vat_chat AS facilities, ngay_het_han_hop_dong AS contract_end_date
        FROM phong_tro
        WHERE id_nha_tro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_nha_tro);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Phòng trọ</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
      .container {
        padding: 20px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
      }
      th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }
      th {
        background-color: #f2f2f2;
      }
      button {
        margin: 5px;
        padding: 5px 10px;
      }
    </style>
  </head>
  <body>
    <!-- Navbar -->
    <nav>
      <ul>
        <li><a href="./index.php">Báo cáo thống kê</a></li>
        <li><a href="./quanlynguoidung.php">Quản lý khách</a></li>
        <li><a href="./quanlynhatro.php">Quản lý Nhà trọ</a></li>
        <li><a href="./quanlygiaodich.php">Quản lý Hoá đơn</a></li>
        <li><a href="./quanlydichvu.php">Quản lý Dịch vụ</a></li>
        <li><a href="./quanlyhopdong.php">Quản lý Hợp đồng</a></li>
      </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
      <h1>Quản lý Phòng trọ</h1>
      <button class="add-button" onclick="addNewRoom()">Thêm Phòng trọ mới</button>

      <table>
        <thead>
          <tr>
            <th>Số phòng</th>
            <th>Diện tích (m2)</th>
            <th>Giá thuê</th>
            <th>Số người tối đa</th>
            <th>Trạng thái</th>
            <th>Cơ sở vật chất</th>
            <th>Ngày hết hạn hợp đồng</th>
            <th>Chức năng</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['room_id']); ?></td>
                <td><?php echo htmlspecialchars($row['area']); ?></td>
                <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VND</td>
                <td><?php echo htmlspecialchars($row['max_people']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['facilities'])); ?></td>
                <td><?php echo $row['contract_end_date'] ? htmlspecialchars($row['contract_end_date']) : 'N/A'; ?></td>
                <td>
                  <button class="edit-button" onclick="editRoom('<?php echo $row['room_id']; ?>')">Sửa</button>
                  <button class="delete-button" onclick="deleteRoom('<?php echo $row['room_id']; ?>')">Xóa</button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">Không có dữ liệu phòng trọ.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <script>
      function addNewRoom() {
        alert("Chức năng thêm phòng trọ mới sẽ được triển khai ở đây.");
      }

      function editRoom(id) {
        alert(`Sửa thông tin phòng trọ có ID: ${id}`);
      }

      function deleteRoom(id) {
        if (confirm(`Bạn có chắc chắn muốn xóa phòng trọ có ID: ${id}?`)) {
          alert(`Đã xóa phòng trọ có ID: ${id}`);
          // Gửi yêu cầu xóa đến server (cần triển khai thêm API xóa)
        }
      }
    </script>
  </body>
</html>