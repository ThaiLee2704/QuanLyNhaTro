<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Lấy ID nhà trọ từ URL
$id_nha_tro = $_GET['id'] ?? null;
if (!$id_nha_tro) die("Không tìm thấy ID nhà trọ.");

// Số dòng mỗi trang
$limit = 5;

// Trang hiện tại (mặc định là 1)
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Tính offset
$offset = ($page - 1) * $limit;

// Đếm tổng số phòng trọ của nhà trọ này
$count_sql = "SELECT COUNT(*) AS total FROM phong_tro WHERE id_nha_tro = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $id_nha_tro);
$count_stmt->execute();
$count_stmt->bind_result($total_rooms);
$count_stmt->fetch();
$count_stmt->close();
$total_pages = ceil($total_rooms / $limit);

// Truy vấn dữ liệu từ bảng phong_tro
$sql = "SELECT id_phong_tro AS room_id, dien_tich AS area, gia_thue AS price, 
               so_nguoi_toi_da AS max_people, trang_thai AS status, 
               co_so_vat_chat AS facilities, ngay_het_han_hop_dong AS contract_end_date
        FROM phong_tro
        WHERE id_nha_tro = ?
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_nha_tro, $limit, $offset);
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
      .container { padding: 20px; }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
      th { background-color: #f2f2f2; }
      button { margin: 5px; padding: 5px 10px; }
      #modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 999;
      }
      #addRoomModal, #editRoomModal {
        display: none; position: fixed; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: #fff; padding: 20px; border-radius: 8px; z-index: 1000;
        width: 350px; max-width: 95vw;
      }
      #addRoomModal input, #addRoomModal select, #addRoomModal textarea,
      #editRoomModal input, #editRoomModal select, #editRoomModal textarea {
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
      <h1>Quản lý Phòng trọ</h1>
      <button class="add-button" onclick="openAddRoomModal()">Thêm Phòng trọ mới</button>
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
              <tr data-room='<?php echo json_encode($row); ?>'>
                <td><?php echo htmlspecialchars($row['room_id']); ?></td>
                <td><?php echo htmlspecialchars($row['area']); ?></td>
                <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VND</td>
                <td><?php echo htmlspecialchars($row['max_people']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['facilities'])); ?></td>
                <td><?php echo $row['contract_end_date'] ? htmlspecialchars($row['contract_end_date']) : 'N/A'; ?></td>
                <td>
                  <button class="edit-button" onclick="openEditRoomModal(this)">Sửa</button>
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

      <!-- Phân trang -->
      <div class="pagination" style="margin-top:20px;text-align:center;">
        <?php if ($total_pages > 1): ?>
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page): ?>
              <strong style="margin:0 5px;"><?php echo $i; ?></strong>
            <?php else: ?>
              <a href="?id=<?php echo $id_nha_tro; ?>&page=<?php echo $i; ?>" style="margin:0 5px;"><?php echo $i; ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Modal thêm phòng trọ -->
    <div id="modal-overlay"></div>
    <div id="addRoomModal">
      <h2>Thêm phòng trọ mới</h2>
      <form id="addRoomForm">
        <input type="hidden" name="id_nha_tro" value="<?php echo htmlspecialchars($id_nha_tro); ?>">
        <label>Diện tích (m2):</label>
        <input type="number" name="area" min="1" step="0.1" required>
        <label>Giá thuê:</label>
        <input type="number" name="price" min="0" required>
        <label>Số người tối đa:</label>
        <input type="number" name="max_people" min="1" required>
        <label>Trạng thái:</label>
        <select name="status" required>
          <option value="Trống">Trống</option>
          <option value="Đã thuê">Đã thuê</option>
        </select>
        <label>Cơ sở vật chất:</label>
        <textarea name="facilities" rows="2"></textarea>
        <label>Ngày hết hạn hợp đồng:</label>
        <input type="date" name="contract_end_date">
        <button type="submit">Thêm</button>
        <button type="button" onclick="closeAddRoomModal()">Hủy</button>
      </form>
    </div>

    <!-- Modal sửa phòng trọ -->
    <div id="editRoomModal">
      <h2>Sửa phòng trọ</h2>
      <form id="editRoomForm">
        <input type="hidden" name="room_id" id="edit_room_id">
        <label>Diện tích (m2):</label>
        <input type="number" name="area" id="edit_area" min="1" step="0.1" required>
        <label>Giá thuê:</label>
        <input type="number" name="price" id="edit_price" min="0" required>
        <label>Số người tối đa:</label>
        <input type="number" name="max_people" id="edit_max_people" min="1" required>
        <label>Trạng thái:</label>
        <select name="status" id="edit_status" required>
          <option value="Trống">Trống</option>
          <option value="Đã thuê">Đã thuê</option>
        </select>
        <label>Cơ sở vật chất:</label>
        <textarea name="facilities" id="edit_facilities" rows="2"></textarea>
        <label>Ngày hết hạn hợp đồng:</label>
        <input type="date" name="contract_end_date" id="edit_contract_end_date">
        <button type="submit">Lưu</button>
        <button type="button" onclick="closeEditRoomModal()">Hủy</button>
      </form>
    </div>

    <script>
      function openAddRoomModal() {
        document.getElementById('addRoomModal').style.display = 'block';
        document.getElementById('modal-overlay').style.display = 'block';
      }
      function closeAddRoomModal() {
        document.getElementById('addRoomModal').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
      }
      function openEditRoomModal(btn) {
        const row = btn.closest('tr');
        const data = JSON.parse(row.getAttribute('data-room'));
        document.getElementById('edit_room_id').value = data.room_id;
        document.getElementById('edit_area').value = data.area;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_max_people').value = data.max_people;
        document.getElementById('edit_status').value = data.status;
        document.getElementById('edit_facilities').value = data.facilities;
        document.getElementById('edit_contract_end_date').value = data.contract_end_date || '';
        document.getElementById('editRoomModal').style.display = 'block';
        document.getElementById('modal-overlay').style.display = 'block';
      }
      function closeEditRoomModal() {
        document.getElementById('editRoomModal').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
      }
      document.getElementById('modal-overlay').onclick = function() {
        closeAddRoomModal();
        closeEditRoomModal();
      };

      // Thêm phòng trọ
      document.getElementById('addRoomForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('add_room.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Thêm phòng trọ thành công!');
            location.reload();
          } else {
            alert('Lỗi: ' + data.message);
          }
        })
        .catch(() => alert('Đã xảy ra lỗi khi thêm phòng trọ!'));
      };

      // Sửa phòng trọ
      document.getElementById('editRoomForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('edit_room.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Cập nhật phòng trọ thành công!');
            location.reload();
          } else {
            alert('Lỗi: ' + data.message);
          }
        })
        .catch(() => alert('Đã xảy ra lỗi khi sửa phòng trọ!'));
      };

      // Xóa phòng trọ
      function deleteRoom(roomId) {
        if (confirm(`Bạn có chắc chắn muốn xóa phòng trọ có ID: ${roomId}?`)) {
          fetch('delete_room.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'room_id=' + encodeURIComponent(roomId)
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert('Đã xóa phòng trọ!');
              location.reload();
            } else {
              alert('Lỗi: ' + data.message);
            }
          })
          .catch(() => alert('Đã xảy ra lỗi khi xóa phòng trọ!'));
        }
      }
    </script>
  </body>
</html>