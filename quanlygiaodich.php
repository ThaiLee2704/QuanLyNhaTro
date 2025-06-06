<?php
// Bạn có thể thêm logic PHP ở đây nếu cần
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quản lý Giao dịch</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .status-toggle {
      cursor: pointer;
      padding: 5px 10px;
      border-radius: 5px;
    }
    .paid {
      background-color: #4CAF50;
      color: white;
    }
    .unpaid {
      background-color: #f44336;
      color: white;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    #roomSelect {
      display: none; /* Hide room dropdown initially */
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
    <h1>Quản lý Hoá đơn</h1>

    <!-- Nút thêm giao dịch sẽ được hiển thị sau khi chọn tòa nhà -->
    <button id="addTransactionBtn" style="display: none;">Thêm Hoá đơn mới</button>

    <!-- Dropdown Tòa nhà -->
    <select id="buildingSelect" class="building-dropdown">
      <option value="">Tất cả tòa nhà</option>
      <option value="01">Nhà trọ ABC</option>
      <option value="02">Nhà trọ XYZ</option>
    </select>

    <!-- Dropdown for Rooms -->
    <select id="roomSelect" class="room-dropdown">
      <option value="">Tất cả phòng</option>
      <!-- Room options will be populated dynamically -->
    </select>

    <table id="transactionTable">
      <thead>
        <tr>
          <th>Mã hoá đơn</th>
          <th>Tên khách</th>
          <th>Số phòng</th>
          <th>Ngày đóng tiền</th>
          <th>Tổng số tiền</th>
          <th>Phương thức thanh toán</th>
          <th>Trạng thái</th>
          <th>Chi tiết</th>
        </tr>
      </thead>
      <tbody id="transactionTableBody">
        <!-- Data will be populated by JavaScript -->
      </tbody>
    </table>
  </div>

  <!-- Modal Popup for Adding New Transaction -->
  <div id="transactionModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Thêm Hoá đơn mới</h2>
      <form id="transactionForm">
        <label for="transactionBuilding">Tòa nhà:</label>
        <select id="transactionBuilding" name="transactionBuilding" required>
          <option value="">Chọn tòa nhà</option>
          <option value="01">Nhà trọ ABC</option>
          <option value="02">Nhà trọ XYZ</option>
        </select>

        <label for="transactionRoom">Phòng:</label>
        <select id="transactionRoom" name="transactionRoom" required>
          <option value="">Chọn phòng</option>
          <!-- Room options will be populated dynamically -->
        </select>

        <label for="customerName">Tên khách:</label>
        <input type="text" id="customerName" name="customerName" required />

        <label for="transactionDate">Ngày Giao dịch:</label>
        <input type="date" id="transactionDate" name="transactionDate" required />

        <!-- <label for="totalAmount">Tổng số tiền:</label>
        <input type="number" id="totalAmount" name="totalAmount" required /> -->

        <label for="paymentMethod">Phương thức thanh toán:</label>
        <select id="paymentMethod" name="paymentMethod" required>
          <option value="cash">Tiền mặt</option>
          <option value="transfer">Chuyển khoản</option>
          <option value="card">Thẻ</option>
        </select>

        <button type="submit">Lưu Hoá đơn</button>
      </form>
    </div>
  </div>

  <script>
    const buildingSelect = document.getElementById('buildingSelect');
    const roomSelect = document.getElementById('roomSelect');
    const transactionTableBody = document.getElementById('transactionTableBody');
    const addTransactionBtn = document.getElementById('addTransactionBtn');
    const modal = document.getElementById('transactionModal');
    const closeBtn = document.querySelector('.close');

    // Mock data for rooms
    const rooms = {
      '01': ['Phòng 101', 'Phòng 102', 'Phòng 103'],
      '02': ['Phòng 201', 'Phòng 202', 'Phòng 203']
    };

    // Mock data for transactions
    const transactions = [
      { id: 1, customerName: 'Nguyễn Văn A', room: 'Phòng 101', date: '2024-09-01', amount: 1000000, paymentMethod: 'cash', details: 'Tiền phòng', status: 'paid' },
      { id: 2, customerName: 'Trần Thị B', room: 'Phòng 201', date: '2024-09-02', amount: 1200000, paymentMethod: 'transfer', details: 'Tiền phòng', status: 'unpaid' },
      // Add more mock transactions as needed
    ];

    function populateRoomDropdown(buildingId) {
      roomSelect.innerHTML = '<option value="">Chọn phòng</option>';  // Default option
      if (buildingId && rooms[buildingId]) {
        // Populate room dropdown based on selected building
        rooms[buildingId].forEach(room => {
          const option = document.createElement('option');
          option.value = room;
          option.textContent = room;
          roomSelect.appendChild(option);
        });
        roomSelect.style.display = 'inline-block';  // Show room dropdown
      } else {
        roomSelect.style.display = 'none';  // Hide room dropdown if no building is selected
      }
    }

    buildingSelect.addEventListener('change', function() {
      const selectedBuilding = this.value;
      populateRoomDropdown(selectedBuilding);  // Update rooms dropdown
      populateTransactionTable(selectedBuilding, '');  // Filter transactions by selected building

      // Hiển thị nút thêm giao dịch khi có tòa nhà được chọn
      addTransactionBtn.style.display = selectedBuilding ? 'inline-block' : 'none';

      // Reset and update the room selection in the modal form
      const transactionRoomSelect = document.getElementById('transactionRoom');
      transactionRoomSelect.innerHTML = '<option value="">Chọn phòng</option>';
      if (selectedBuilding && rooms[selectedBuilding]) {
        rooms[selectedBuilding].forEach(room => {
          const option = document.createElement('option');
          option.value = room;
          option.textContent = room;
          transactionRoomSelect.appendChild(option);
        });
      }
    });

    roomSelect.addEventListener('change', function() {
      const selectedBuilding = buildingSelect.value;
      const selectedRoom = this.value;
      populateTransactionTable(selectedBuilding, selectedRoom);
    });

    function populateTransactionTable(buildingId, roomId) {
      transactionTableBody.innerHTML = ''; // Clear previous entries

      // Filter transactions based on the selected building and room
      transactions.forEach(transaction => {
        const isBuildingMatch = buildingId === '' || (buildingId === '01' && transaction.room.startsWith('Phòng 101')) || 
                                (buildingId === '02' && transaction.room.startsWith('Phòng 201'));
        const isRoomMatch = roomId === '' || transaction.room === roomId;

        if (isBuildingMatch && isRoomMatch) {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${transaction.id}</td>
            <td>${transaction.customerName}</td>
            <td>${transaction.room}</td>
            <td>${transaction.date}</td>
            <td>${transaction.amount}</td>
            <td>${transaction.paymentMethod}</td>
            <td>
              <button class="status-toggle ${transaction.status}" data-id="${transaction.id}">${transaction.status === 'paid' ? 'Đã thu' : 'Chưa thu'}</button>
            </td>
            <td>
              <a href="chitietgiaodich.php?id=${transaction.id}">Xem Chi tiết</a> 
              <button class="delete-btn" data-id="${transaction.id}">Xóa</button>
            </td>
          `;

          transactionTableBody.appendChild(row);
        }
      });

      // Xử lý sự kiện cho nút "Xóa"
      document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
          const transactionId = this.getAttribute('data-id');
          if (confirm(`Bạn có chắc chắn muốn xóa giao dịch ${transactionId}?`)) {
            // Xóa giao dịch khỏi danh sách
            const index = transactions.findIndex(t => t.id == transactionId);
            if (index > -1) {
              transactions.splice(index, 1);
              populateTransactionTable(buildingId, roomId); // Cập nhật lại bảng giao dịch
            }
          }
        });
      });

      // Xử lý sự kiện cho nút "Thay đổi trạng thái"
      document.querySelectorAll('.status-toggle').forEach(button => {
        button.addEventListener('click', function() {
          const transactionId = this.getAttribute('data-id');
          const transaction = transactions.find(t => t.id == transactionId);
          if (transaction) {
            transaction.status = transaction.status === 'paid' ? 'unpaid' : 'paid'; // Chuyển đổi trạng thái
            populateTransactionTable(buildingId, roomId); // Cập nhật lại bảng giao dịch
          }
        });
      });
    }

    // Open modal
    addTransactionBtn.onclick = function() {
      modal.style.display = "block";
    }

    // Close modal
    closeBtn.onclick = function() {
      modal.style.display = "none";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    // Handle form submission for adding a new transaction
    document.getElementById('transactionForm').onsubmit = function(event) {
      event.preventDefault();
      const newTransaction = {
        id: transactions.length + 1, // Tạo mã GD mới
        customerName: document.getElementById('customerName').value,
        room: document.getElementById('transactionRoom').value,
        date: document.getElementById('transactionDate').value,
        amount: parseInt(document.getElementById('totalAmount').value),
        paymentMethod: document.getElementById('paymentMethod').value,
        details: 'Tiền phòng',
        status: 'unpaid' // Mặc định là chưa thu
      };
      transactions.push(newTransaction); // Thêm giao dịch mới vào danh sách
      populateTransactionTable(buildingSelect.value, roomSelect.value); // Cập nhật bảng giao dịch
      modal.style.display = "none"; // Đóng modal
      this.reset(); // Reset form
    }
  </script>
</body>
</html>