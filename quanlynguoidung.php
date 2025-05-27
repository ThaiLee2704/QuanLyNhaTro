<?php
// Kết nối cơ sở dữ liệu
include 'db.php';

// Truy vấn dữ liệu từ bảng khach_hang
$sql = "SELECT kh.id_khach AS id, kh.ho_ten AS name, kh.gioi_tinh AS gender, kh.ngay_sinh AS dob, 
               kh.sdt AS phone, kh.email, kh.cccd, kh.dia_chi_thuong_tru AS address, 
               nt.id_nha_tro AS house_id, nt.ten_nha_tro AS house, pt.id_phong_tro AS room_id, pt.id_phong_tro AS room, kh.anh AS photo
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
      .button-group { margin-bottom: 20px; }
      .button-group button { margin-right: 10px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
      th { background-color: #f2f2f2; }
      #addCustomerModal, #editCustomerModal {
        display: none; position: fixed; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 40%; background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000; border-radius: 8px; padding: 20px;
      }
      #addCustomerModal .modal-content, #editCustomerModal .modal-content {
        max-height: 80vh; overflow-y: auto; width: 100%; margin: auto auto;
      }
      #addCustomerModal button, #editCustomerModal button { margin-top: 10px; }
      #modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5); z-index: 999;
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
    <div class="container">
      <h1>Quản lý khách hàng</h1>
      <div class="button-group">
        <button onclick="addNewCustomer()">Thêm khách hàng mới</button>
        <button onclick="exportPDF()">Xuất báo cáo PDF</button>
      </div>
      <table id="customerTable">
        <thead>
          <tr>
            <th>ID khách hàng</th>
            <th>Ảnh</th>
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
              <tr data-customer='<?php echo json_encode($row); ?>'>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td>
                  <?php if (!empty($row['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Ảnh" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                  <?php else: ?>
                    <span style="color:#aaa;">Không có</span>
                  <?php endif; ?>
                </td>
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
                  <button onclick="openEditCustomerModal(this)">Sửa</button>
                  <button onclick="deleteCustomer('<?php echo $row['id']; ?>')">Xóa</button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="12">Không có dữ liệu khách hàng.</td>
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
        <form id="addCustomerForm" enctype="multipart/form-data">
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
          <label for="contract_end_date">Ngày hết hạn hợp đồng:</label>
          <input type="date" id="contract_end_date" name="contract_end_date" required><br><br>
          <label for="photo">Ảnh khách hàng:</label>
          <input type="file" id="photo" name="photo" accept="image/*"><br><br>
          <button type="submit">Thêm</button>
          <button type="button" onclick="closeModal()">Hủy</button>
        </form>
      </div>
    </div>

    <!-- Modal sửa khách hàng -->
    <div id="editCustomerModal">
      <div class="modal-content">
        <h2>Sửa khách hàng</h2>
        <form id="editCustomerForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="edit_id">
          <label for="edit_name">Họ tên:</label>
          <input type="text" id="edit_name" name="name" required><br><br>
          <label for="edit_gender">Giới tính:</label>
          <select id="edit_gender" name="gender" required>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
          </select><br><br>
          <label for="edit_dob">Ngày sinh:</label>
          <input type="date" id="edit_dob" name="dob"><br><br>
          <label for="edit_phone">Số điện thoại:</label>
          <input type="text" id="edit_phone" name="phone" required><br><br>
          <label for="edit_email">Email:</label>
          <input type="email" id="edit_email" name="email"><br><br>
          <label for="edit_cccd">CCCD:</label>
          <input type="text" id="edit_cccd" name="cccd" required><br><br>
          <label for="edit_address">Địa chỉ thường trú:</label>
          <input type="text" id="edit_address" name="address"><br><br>
          <label for="edit_house">Nhà thuê:</label>
          <select id="edit_house" name="house" required>
            <option value="">Chọn nhà</option>
            <?php
            $houses = $conn->query("SELECT id_nha_tro, ten_nha_tro FROM nha_tro");
            while ($house = $houses->fetch_assoc()) {
              echo "<option value='{$house['id_nha_tro']}'>{$house['ten_nha_tro']}</option>";
            }
            ?>
          </select><br><br>
          <label for="edit_room">Phòng thuê:</label>
          <select id="edit_room" name="room" required>
            <option value="">Chọn phòng</option>
          </select><br><br>
          <label for="edit_photo">Ảnh khách hàng (chọn để thay đổi):</label>
          <input type="file" id="edit_photo" name="photo" accept="image/*"><br><br>
          <img id="current_photo" src="" alt="Ảnh hiện tại" style="width:40px;height:40px;object-fit:cover;border-radius:50%;display:none;"><br>
          <button type="submit">Lưu</button>
          <button type="button" onclick="closeEditModal()">Hủy</button>
        </form>
      </div>
    </div>

    <script src="https://unpkg.com/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="Roboto-normal.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script>
// Hiện modal thêm khách hàng
function addNewCustomer() {
  document.getElementById('addCustomerModal').style.display = 'block';
  document.getElementById('modal-overlay').style.display = 'block';
}
// Đóng modal thêm khách hàng
function closeModal() {
  document.getElementById('addCustomerModal').style.display = 'none';
  document.getElementById('modal-overlay').style.display = 'none';
}
// Đóng modal sửa khách hàng
function closeEditModal() {
  document.getElementById('editCustomerModal').style.display = 'none';
  document.getElementById('modal-overlay').style.display = 'none';
}

// Tải danh sách phòng theo nhà trọ
function loadRooms(houseId, roomSelectId, selectedRoom = '') {
  const roomSelect = document.getElementById(roomSelectId);
  roomSelect.innerHTML = '<option value="">Chọn phòng</option>';
  if (houseId) {
    fetch(`get_rooms.php?id_nha_tro=${houseId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(room => {
          const option = document.createElement('option');
          option.value = room.id_phong_tro;
          option.textContent = `Phòng ${room.id_phong_tro}`;
          roomSelect.appendChild(option);
        });
        if (selectedRoom) roomSelect.value = selectedRoom;
      });
  }
}

// Sự kiện thay đổi nhà trọ khi thêm/sửa
document.getElementById('house').addEventListener('change', function () {
  loadRooms(this.value, 'room');
});
document.getElementById('edit_house').addEventListener('change', function () {
  loadRooms(this.value, 'edit_room');
});

// Thêm khách hàng
document.getElementById('addCustomerForm').addEventListener('submit', function (e) {
  e.preventDefault();
  fetch('add_customer.php', {
    method: 'POST',
    body: new FormData(this)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Thêm khách hàng thành công!');
        location.reload();
      } else {
        alert('Đã xảy ra lỗi khi thêm khách hàng.');
      }
    })
    .catch(() => alert('Đã xảy ra lỗi khi thêm khách hàng.'));
});

// Mở modal sửa khách hàng và đổ dữ liệu
function openEditCustomerModal(btn) {
  const data = JSON.parse(btn.closest('tr').getAttribute('data-customer'));
  document.getElementById('edit_id').value = data.id;
  document.getElementById('edit_name').value = data.name;
  document.getElementById('edit_gender').value = data.gender;
  document.getElementById('edit_dob').value = data.dob;
  document.getElementById('edit_phone').value = data.phone;
  document.getElementById('edit_email').value = data.email;
  document.getElementById('edit_cccd').value = data.cccd;
  document.getElementById('edit_address').value = data.address;
  document.getElementById('edit_house').value = data.house_id || '';
  loadRooms(data.house_id, 'edit_room', data.room_id || '');
  // Hiện ảnh hiện tại nếu có
  const img = document.getElementById('current_photo');
  if (data.photo) {
    img.src = data.photo;
    img.style.display = 'inline-block';
  } else {
    img.style.display = 'none';
  }
  document.getElementById('editCustomerModal').style.display = 'block';
  document.getElementById('modal-overlay').style.display = 'block';
}

// Sửa khách hàng
document.getElementById('editCustomerForm').addEventListener('submit', function (e) {
  e.preventDefault();
  fetch('edit_customer.php', {
    method: 'POST',
    body: new FormData(this)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Cập nhật khách hàng thành công!');
        location.reload();
      } else {
        alert('Đã xảy ra lỗi khi sửa khách hàng.');
      }
    })
    .catch(() => alert('Đã xảy ra lỗi khi sửa khách hàng.'));
});

// Xóa khách hàng
function deleteCustomer(id) {
  if (confirm('Bạn có chắc chắn muốn xóa khách hàng này?')) {
    fetch('delete_customer.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'id=' + encodeURIComponent(id)
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Đã xóa khách hàng!');
          location.reload();
        } else {
          alert('Lỗi: ' + data.message);
        }
      })
      .catch(() => alert('Đã xảy ra lỗi khi xóa khách hàng!'));
  }
}

// Xuất PDF danh sách khách hàng (bỏ cột chức năng, font Unicode, căn bảng đẹp)
function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });

  doc.setFont("DejaVuSans", "normal"); // Font Unicode hỗ trợ tiếng Việt
  doc.setFontSize(13);
  doc.text("Báo cáo danh sách khách hàng", 14, 16);

  // Lấy dữ liệu bảng, bỏ cột cuối
  const table = document.getElementById("customerTable");
  let head = [], body = [];
  table.querySelectorAll("thead tr").forEach(tr => {
    let row = [];
    let ths = tr.querySelectorAll("th");
    for (let i = 0; i < ths.length - 1; i++) row.push(ths[i].innerText);
    head.push(row);
  });
  table.querySelectorAll("tbody tr").forEach(tr => {
    let row = [];
    let tds = tr.querySelectorAll("td");
    for (let i = 0; i < tds.length - 1; i++) {
      row.push(i === 1 ? (tds[i].querySelector('img') ? "Có" : "Không") : tds[i].innerText.trim());
    }
    body.push(row);
  });

  doc.autoTable({
    head: head,
    body: body,
    startY: 22,
    margin: { left: 8, right: 8 },
    styles: { font: "DejaVuSans", fontSize: 10, cellPadding: 2, overflow: 'linebreak' },
    headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
    bodyStyles: { textColor: 20 },
    tableWidth: 'auto'
  });
  doc.save("bao_cao_khach_hang.pdf");
}
    </script>
  </body>
</html>