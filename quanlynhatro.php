<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Số dòng mỗi trang
$limit = 5;

// Trang hiện tại (mặc định là 1)
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Tính offset
$offset = ($page - 1) * $limit;

// Đếm tổng số nhà trọ
$total_sql = "SELECT COUNT(*) AS total FROM nha_tro";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_houses = $total_row['total'];
$total_pages = ceil($total_houses / $limit);

// Truy vấn dữ liệu từ bảng nha_tro và đếm số phòng, số phòng trống
$sql = "SELECT 
            nt.id_nha_tro AS id, 
            nt.ten_nha_tro AS name, 
            nt.dia_chi_nha_tro AS address,
            COUNT(pt.id_phong_tro) AS total_rooms,
            SUM(CASE WHEN pt.trang_thai = 'Trống' THEN 1 ELSE 0 END) AS empty_rooms
        FROM nha_tro nt
        LEFT JOIN phong_tro pt ON nt.id_nha_tro = pt.id_nha_tro
        GROUP BY nt.id_nha_tro, nt.ten_nha_tro, nt.dia_chi_nha_tro
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Nhà trọ</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
      .container { padding: 20px; }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
      th { background-color: #f2f2f2; }
      button { margin: 5px; padding: 5px 10px; }
      #modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 999;
      }
      #addHouseModal, #editHouseModal {
        display: none; position: fixed; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: #fff; padding: 20px; border-radius: 8px; z-index: 1000;
        width: 350px; max-width: 95vw;
      }
      #addHouseModal input, #editHouseModal input {
        width: 100%; margin-bottom: 10px; padding: 5px;
      }
      .pagination a {
        padding: 4px 10px;
        background: #eee;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
      }
      .pagination strong {
        color: #fff;
        background: #2980b9;
        padding: 4px 10px;
        border-radius: 4px;
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
      <button onclick="openAddHouseModal()">Thêm Nhà trọ mới</button>
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
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr data-house='<?php echo json_encode($row); ?>'>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo (int)$row['total_rooms']; ?></td>
                <td><?php echo (int)$row['empty_rooms']; ?></td>
                <td>
                  <button onclick="viewBoardingHouse('<?php echo $row['id']; ?>')">Xem</button>
                  <button onclick="openEditHouseModal(this)">Sửa</button>
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

      <!-- Phân trang -->
      <div class="pagination" style="margin-top:20px;text-align:center;">
        <?php if ($total_pages > 1): ?>
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page): ?>
              <strong style="margin:0 5px;"><?php echo $i; ?></strong>
            <?php else: ?>
              <a href="?page=<?php echo $i; ?>" style="margin:0 5px;"><?php echo $i; ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Modal thêm nhà trọ -->
    <div id="modal-overlay"></div>
    <div id="addHouseModal">
      <h2>Thêm nhà trọ mới</h2>
      <form id="addHouseForm">
        <label>Tên nhà trọ:</label>
        <input type="text" name="ten_nha_tro" required>
        <label>Địa chỉ nhà trọ:</label>
        <input type="text" name="dia_chi_nha_tro" required>
        <button type="submit">Thêm</button>
        <button type="button" onclick="closeAddHouseModal()">Hủy</button>
      </form>
    </div>

    <!-- Modal sửa nhà trọ -->
    <div id="editHouseModal">
      <h2>Sửa nhà trọ</h2>
      <form id="editHouseForm">
        <input type="hidden" name="id_nha_tro" id="edit_id_nha_tro">
        <label>Tên nhà trọ:</label>
        <input type="text" name="ten_nha_tro" id="edit_ten_nha_tro" required>
        <label>Địa chỉ nhà trọ:</label>
        <input type="text" name="dia_chi_nha_tro" id="edit_dia_chi_nha_tro" required>
        <button type="submit">Lưu</button>
        <button type="button" onclick="closeEditHouseModal()">Hủy</button>
      </form>
    </div>

    <script>
      function openAddHouseModal() {
        document.getElementById('addHouseModal').style.display = 'block';
        document.getElementById('modal-overlay').style.display = 'block';
      }
      function closeAddHouseModal() {
        document.getElementById('addHouseModal').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
      }
      function openEditHouseModal(btn) {
        const row = btn.closest('tr');
        const data = JSON.parse(row.getAttribute('data-house'));
        document.getElementById('edit_id_nha_tro').value = data.id;
        document.getElementById('edit_ten_nha_tro').value = data.name;
        document.getElementById('edit_dia_chi_nha_tro').value = data.address;
        document.getElementById('editHouseModal').style.display = 'block';
        document.getElementById('modal-overlay').style.display = 'block';
      }
      function closeEditHouseModal() {
        document.getElementById('editHouseModal').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
      }
      document.getElementById('modal-overlay').onclick = function() {
        closeAddHouseModal();
        closeEditHouseModal();
      };

      // Thêm nhà trọ
      document.getElementById('addHouseForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('add_house.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Thêm nhà trọ thành công!');
            location.reload();
          } else {
            alert('Lỗi: ' + data.message);
          }
        })
        .catch(() => alert('Đã xảy ra lỗi khi thêm nhà trọ!'));
      };

      // Sửa nhà trọ
      document.getElementById('editHouseForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('edit_house.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Cập nhật nhà trọ thành công!');
            location.reload();
          } else {
            alert('Lỗi: ' + data.message);
          }
        })
        .catch(() => alert('Đã xảy ra lỗi khi sửa nhà trọ!'));
      };

      // Xóa nhà trọ
      function deleteBoardingHouse(id) {
        if (confirm(`Bạn có chắc chắn muốn xóa nhà trọ có ID: ${id}?`)) {
          fetch('delete_house.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id_nha_tro=' + encodeURIComponent(id)
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert('Đã xóa nhà trọ!');
              location.reload();
            } else {
              alert('Lỗi: ' + data.message);
            }
          })
          .catch(() => alert('Đã xảy ra lỗi khi xóa nhà trọ!'));
        }
      }

      function viewBoardingHouse(id) {
        window.location.href = `./quanlyphongtro.php?id=${id}`;
      }
    </script>
  </body>
</html>