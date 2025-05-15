<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Truy vấn dữ liệu từ bảng khach_hang
$sql = "SELECT kh.id_khach AS id, kh.ho_ten AS name, kh.gioi_tinh AS gender, kh.ngay_sinh AS dob, 
               kh.sdt AS phone, kh.email, kh.cccd, kh.dia_chi_thuong_tru AS address, 
               nt.ten_nha_tro AS house, pt.id_phong_tro AS room
        FROM khach_hang kh
        LEFT JOIN nha_tro nt ON kh.id_nha_tro = nt.id_nha_tro
        LEFT JOIN phong_tro pt ON kh.id_phong_tro = pt.id_phong_tro";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
      .button-group {
        margin-bottom: 20px;
      }
      .button-group button {
        margin-right: 10px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }
      th {
        background-color: #f2f2f2;
      }
      #addCustomerModal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40%;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        border-radius: 8px;
        padding: 20px;
      }
      #addCustomerModal .modal-content {
        max-height: 80vh;
        overflow-y: auto;
      }
      #addCustomerModal button {
        margin-top: 10px;
      }
      #modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
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
      <h1>Quản lý khách hàng</h1>
      <div class="button-group">
        <button onclick="addNewCustomer()">Thêm khách hàng mới</button>
        <button onclick="exportReport()">Xuất báo cáo</button>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>ID khách hàng</th>
            <th>Họ tên</th>
            <th>Giới tính</th>
            <th>Ngày Sinh</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>CCCD</th>
            <th>Địa chỉ thường trú</th>
            <th>Nhà thuê</th>
            <th>Phòng thuê</th>
            <th>Chức năng</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['dob']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['cccd']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['house']); ?></td>
                <td><?php echo htmlspecialchars($row['room']); ?></td>
                <td>
                  <button onclick="editCustomer(this)">Sửa</button>
                  <button onclick="deleteCustomer(this)">Xóa</button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="11">Không có dữ liệu khách hàng.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Modal thêm khách hàng -->
    <div id="modal-overlay"></div>
    <div id="addCustomerModal">
      <div class="modal-content">
        <h2>Thêm khách hàng mới</h2>
        <form id="addCustomerForm">
          <label for="name">Họ tên:</label>
          <input type="text" id="name" name="name" required><br><br>

          <label for="gender">Giới tính:</label>
          <select id="gender" name="gender" required>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
          </select><br><br>

          <label for="dob">Ngày sinh:</label>
          <input type="date" id="dob" name="dob"><br><br>

          <label for="phone">Số điện thoại:</label>
          <input type="text" id="phone" name="phone" required><br><br>

          <label for="email">Email:</label>
          <input type="email" id="email" name="email"><br><br>

          <label for="cccd">CCCD:</label>
          <input type="text" id="cccd" name="cccd" required><br><br>

          <label for="address">Địa chỉ thường trú:</label>
          <input type="text" id="address" name="address"><br><br>

          <label for="house">Nhà thuê:</label>
          <select id="house" name="house" required>
            <option value="">Chọn nhà</option>
            <?php
            $houses = $conn->query("SELECT id_nha_tro, ten_nha_tro FROM nha_tro");
            while ($house = $houses->fetch_assoc()) {
              echo "<option value='{$house['id_nha_tro']}'>{$house['ten_nha_tro']}</option>";
            }
            ?>
          </select><br><br>

          <label for="room">Phòng thuê:</label>
          <select id="room" name="room" required>
            <option value="">Chọn phòng</option>
          </select><br><br>

          <button type="submit">Thêm</button>
          <button type="button" onclick="closeModal()">Hủy</button>
        </form>
      </div>
    </div>

    <script>
      // Mở modal
      function addNewCustomer() {
        document.getElementById('addCustomerModal').style.display = 'block';
        document.getElementById('modal-overlay').style.display = 'block';
      }

      // Đóng modal
      function closeModal() {
        document.getElementById('addCustomerModal').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
      }

      // Tải danh sách phòng dựa trên nhà thuê
      document.getElementById('house').addEventListener('change', function () {
        const houseId = this.value;
        const roomSelect = document.getElementById('room');
        roomSelect.innerHTML = '<option value="">Chọn phòng</option>'; // Reset danh sách phòng

        if (houseId) {
          fetch(`get_rooms.php?id_nha_tro=${houseId}`)
            .then(response => response.json())
            .then(data => {
              data.forEach(room => {
                const option = document.createElement('option');
                option.value = room.id_phong_tro;
                option.textContent = `Phòng ${room.id_phong_tro}`;
                roomSelect.appendChild(option);
              });
            });
        }
      });

      // Xử lý form thêm khách hàng
      document.getElementById('addCustomerForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('add_customer.php', {
          method: 'POST',
          body: formData
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Thêm khách hàng thành công!');
              location.reload();
            } else {
              alert('Lỗi: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi thêm khách hàng.');
          });
      });
    </script>
  </body>
</html>