<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Truy vấn dữ liệu từ bảng nha_tro
$sql = "SELECT id_nha_tro AS id, ten_nha_tro AS name, dia_chi_nha_tro AS address, 
               tong_so_phong AS total_rooms, phong_con_trong AS empty_rooms 
        FROM nha_tro";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Nhà trọ</title>
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
      <h1>Quản lý Nhà trọ</h1>
      <button onclick="addNewBoardingHouse()">Thêm Nhà trọ mới</button>
      
      <table>
        <thead>
          <tr>
            <th>ID nhà trọ</th>
            <th>Tên nhà</th>
            <th>Địa chỉ nhà</th>
            <th>Tổng số phòng</th>
            <th>Phòng còn trống</th>
            <th>Chức năng</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['total_rooms']); ?></td>
                <td><?php echo htmlspecialchars($row['empty_rooms']); ?></td>
                <td>
                  <button onclick="viewBoardingHouse('<?php echo $row['id']; ?>')">Xem</button>
                  <button onclick="editBoardingHouse('<?php echo $row['id']; ?>')">Sửa</button>
                  <button onclick="deleteBoardingHouse('<?php echo $row['id']; ?>')">Xóa</button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">Không có dữ liệu nhà trọ.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <script>
      function addNewBoardingHouse() {
        alert("Chức năng thêm nhà trọ mới sẽ được triển khai ở đây.");
      }

      function viewBoardingHouse(id) {
        //alert(`Xem chi tiết nhà trọ có ID: ${id}`);
        // Chuyển hướng đến trang quản lý phòng trọ với ID nhà trọ tương ứng
        window.location.href = `./quanlyphongtro.php?id=${id}`;
      }

      function editBoardingHouse(id) {
        alert(`Sửa thông tin nhà trọ có ID: ${id}`);
      }

      function deleteBoardingHouse(id) {
        if (confirm(`Bạn có chắc chắn muốn xóa nhà trọ có ID: ${id}?`)) {
          alert(`Đã xóa nhà trọ có ID: ${id}`);
          // Gửi yêu cầu xóa đến server (cần triển khai thêm API xóa)
        }
      }
    </script>
  </body>
</html>