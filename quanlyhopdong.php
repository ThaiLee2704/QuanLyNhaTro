<?php
// Bạn có thể thêm logic PHP ở đây nếu cần
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Hợp đồng</title>
    <link rel="stylesheet" href="styles.css" />
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
      <h1>Quản lý Hợp đồng</h1>
      <button>Thêm Hợp đồng mới</button>

      <table>
        <thead>
          <tr>
            <th>Mã HĐ</th>
            <th>Người thuê</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Tiền cọc</th>
            <th>Tiền phòng cố định</th>
            <th>Số phòng</th>
            <th>Trạng thái</th>
            <th>Chức năng</th>
          </tr>
        </thead>
        <tbody>
          <!-- Mockup Data -->
          <tr>
            <td>HD001</td>
            <td>Nguyễn Văn A</td>
            <td>01/01/2024</td>
            <td>01/01/2025</td>
            <td>5,000,000 VND</td>
            <td>3,000,000 VND</td>
            <td>501</td>
            <td>Còn hiệu lực</td>
            <td>
              <button>Sửa</button>
              <button>Xóa</button>
              <button onclick="generateContract('HD001')">Bản cứng HĐ</button>
            </td>
          </tr>
          <tr>
            <td>HD002</td>
            <td>Trần Thị B</td>
            <td>01/02/2024</td>
            <td>01/02/2025</td>
            <td>4,500,000 VND</td>
            <td>2,800,000 VND</td>
            <td> 201</td>
            <td>Đã hết hạn</td>
            <td>
              <button>Sửa</button>
              <button>Xóa</button>
              <button onclick="generateContract('HD002')">Bản cứng HĐ</button>
            </td>
          </tr>
          <tr>
            <td>HD003</td>
            <td>Lê Văn C</td>
            <td>01/03/2024</td>
            <td>01/03/2025</td>
            <td>5,500,000 VND</td>
            <td>3,200,000 VND</td>
            <td>301</td>
            <td>Còn hiệu lực</td>
            <td>
              <button>Sửa</button>
              <button>Xóa</button>
              <button onclick="generateContract('HD003')">Bản cứng HĐ</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <script>
      function generateContract(contractId) {
        // Hàm này sẽ xử lý việc tạo bản cứng hợp đồng
        alert("Đang tạo bản cứng cho hợp đồng " + contractId);
        // Ở đây bạn có thể thêm logic để chuyển hướng đến một trang Word hoặc PDF chứa hợp đồng
      }
    </script>
  </body>
</html>