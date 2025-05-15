<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Truy vấn dữ liệu từ cơ sở dữ liệu

// Lấy số người thuê trọ
$sql_tenants = "SELECT COUNT(*) AS total_tenants FROM khach_hang";
$result_tenants = $conn->query($sql_tenants);
$total_tenants = $result_tenants->fetch_assoc()['total_tenants'] ?? 0;

// Lấy số nhà trọ
$sql_houses = "SELECT COUNT(*) AS total_houses FROM nha_tro";
$result_houses = $conn->query($sql_houses);
$total_houses = $result_houses->fetch_assoc()['total_houses'] ?? 0;

// Lấy số phòng trọ còn trống
$sql_empty_rooms = "SELECT COUNT(*) AS empty_rooms FROM phong_tro WHERE trang_thai = 'Trống'";
$result_empty_rooms = $conn->query($sql_empty_rooms);
$empty_rooms = $result_empty_rooms->fetch_assoc()['empty_rooms'] ?? 0;

// Lấy tổng doanh thu từ bảng hóa đơn
$sql_revenue = "SELECT SUM(tong_so_tien) AS total_revenue FROM hoa_don WHERE trang_thai = 'Đã thanh toán'";
$result_revenue = $conn->query($sql_revenue);
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'] ?? 0;

// Định dạng doanh thu
$total_revenue = number_format($total_revenue, 0, ',', '.') . ' VND';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Báo cáo thống kê</title>
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
      <h1>Dashboard</h1>

      <div class="stats">
        <div class="stat">
          <h3>Số người thuê trọ</h3>
          <p><?php echo $total_tenants; ?></p>
        </div>
        <div class="stat">
          <h3>Số nhà trọ</h3>
          <p><?php echo $total_houses; ?></p>
        </div>
        <div class="stat">
          <h3>Phòng trọ còn trống</h3>
          <p><?php echo $empty_rooms; ?></p>
        </div>
        <div class="stat">
          <h3>Tổng doanh thu</h3>
          <p><?php echo $total_revenue; ?></p>
        </div>
      </div>
    </div>
  </body>
</html>
