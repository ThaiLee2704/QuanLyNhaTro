<?php
// Bạn có thể thêm logic PHP ở đây nếu cần
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chi tiết Giao dịch</title>
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
      <h1>Chi tiết Giao dịch</h1>

      <button onclick="addServiceDetail()">Thêm Chi tiết Dịch vụ</button>
      <table id="serviceTable">
        <thead>
          <tr>
            <th>Tên dịch vụ</th>
            <th>Số lượng</th>
            <th>Đơn giá (VND)</th>
            <th>Thành tiền (VND)</th>
            <th>Chức năng</th>
          </tr>
        </thead>
        <tbody id="serviceTableBody">
          <!-- Data will be populated by JavaScript -->
        </tbody>
      </table>

      <div class="total">
        <h3 id="totalAmount">Tổng số tiền: 0 VND</h3>
      </div>
    </div>

    <script>
      function getUrlParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
      }
    
      const transactionId = getUrlParameter('id');
      const serviceTableBody = document.getElementById('serviceTableBody');
      const totalAmountElement = document.getElementById('totalAmount');
    
      // Default services data (You can modify this to get data dynamically based on transactionId)
      let servicesData = [
        { name: 'Tiền phòng', quantity: '1 tháng', unitPrice: 1000000, total: 1000000 },
        { name: 'Tiền điện', quantity: '100 kWh', unitPrice: 3000, total: 300000 },
        { name: 'Tiền nước', quantity: '10 m³', unitPrice: 10000, total: 100000 }
      ];
    
      // Example of adding Internet service based on transaction ID
      if (transactionId === '2') {
        servicesData.push({ name: 'Tiền Internet', quantity: '1 tháng', unitPrice: 200000, total: 200000 });
      }
    
      function updateTable() {
        serviceTableBody.innerHTML = servicesData.map(service => `
          <tr>
            <td>${service.name}</td>
            <td>${service.quantity}</td>
            <td>${service.unitPrice.toLocaleString()}</td>
            <td>${service.total.toLocaleString()}</td>
            <td>
              <button onclick="editService(this)">Sửa</button>
              <button onclick="deleteService(this)">Xóa</button>
            </td>
          </tr>
        `).join('');
    
        const total = servicesData.reduce((sum, service) => sum + service.total, 0);
        totalAmountElement.textContent = `Tổng số tiền: ${total.toLocaleString()} VND`;
      }
    
      function addServiceDetail() {
        alert('Chức năng thêm chi tiết dịch vụ sẽ được triển khai ở đây.');
      }
    
      function editService(button) {
        const row = button.closest('tr');
        const serviceName = row.cells[0].textContent;
        alert(`Chức năng sửa dịch vụ ${serviceName} sẽ được triển khai ở đây.`);
      }
    
      function deleteService(button) {
        const row = button.closest('tr');
        const serviceName = row.cells[0].textContent;
        if (confirm(`Bạn có chắc chắn muốn xóa dịch vụ ${serviceName}?`)) {
          row.remove();
          updateTotal();
        }
      }
    
      function updateTotal() {
        const rows = serviceTableBody.querySelectorAll('tr');
        let total = 0;
        rows.forEach(row => {
          total += parseInt(row.cells[3].textContent.replace(/,/g, ''));
        });
        totalAmountElement.textContent = `Tổng số tiền: ${total.toLocaleString()} VND`;
      }
    
      // Initialize the table with the services for the transaction ID
      updateTable();
    </script>
    
  </body>
</html>
