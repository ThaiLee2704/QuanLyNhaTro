<?php
// Bạn có thể thêm logic PHP ở đây nếu cần
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Dịch vụ</title>
    <link rel="stylesheet" href="./styles.css">
    <style>
      .building-dropdown {
  margin-top: 20px;
  width: 100%;
  padding: 10px;
  font-size: 16px;
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
      <h1>Quản lý Dịch vụ</h1>
      <button id="addServiceBtn">Thêm Dịch vụ mới</button>
      
      <!-- Dropdown Tòa nhà -->
      <select id="buildingSelect" class="building-dropdown">
        <option value="">Chọn tòa nhà</option>

        <option value="01">Nhà trọ ABC</option>
        <option value="02">Nhà trọ XYZ</option>
      </select>
      
      <!-- Dropdown Dịch vụ -->
      <select id="serviceSelect" class="service-dropdown" style="display: none;">
        <option value="">Chọn dịch vụ</option>
      </select>
      
      <table id="serviceTable" style="display: none;">
        <thead>
          <tr>
            <th>Tên Dịch vụ</th>
            <th>Đơn vị tính</th>
            <th>Đơn giá (VND)</th>
            <th>Chức năng</th>
          </tr>
        </thead>
        <tbody id="serviceTableBody">
        </tbody>
      </table>
    </div>
    
    <!-- Modal Popup for Adding New Service -->
    <div id="serviceModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Thêm Dịch vụ mới</h2>
        <form id="serviceForm">
          <label for="serviceName">Tên Dịch vụ:</label>
          <input type="text" id="serviceName" name="serviceName" required />
          <label for="serviceType">Loại Dịch vụ:</label>
          <select id="serviceType" name="serviceType" required>
            <option value="fixed">Cố định</option>
            <option value="metered">Tính theo số</option>
          </select>
          <label for="unitType">Đơn vị tính:</label>
          <input type="text" id="unitType" name="unitType" required />
          <label for="unitPrice">Đơn giá:</label>
          <input type="number" id="unitPrice" name="unitPrice" required />
          <button type="submit">Lưu Dịch vụ</button>
        </form>
      </div>
    </div>
    
    <script>
      // Modal Popup
      const modal = document.getElementById("serviceModal");
      const btn = document.getElementById("addServiceBtn");
      const closeBtn = document.getElementsByClassName("close")[0];
      
      btn.onclick = function () {
        modal.style.display = "block";
      };
      
      closeBtn.onclick = function () {
        modal.style.display = "none";
      };
      
      window.onclick = function (event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      };
      
      // Form submission
      document
        .getElementById("serviceForm")
        .addEventListener("submit", function (event) {
          event.preventDefault();
          alert("Dịch vụ mới đã được thêm thành công!");
          modal.style.display = "none";
        });
      
      // Xử lý sự kiện khi chọn tòa nhà
      const buildingSelect = document.getElementById('buildingSelect');
      const serviceSelect = document.getElementById('serviceSelect');
      const serviceTable = document.getElementById('serviceTable');
      const serviceTableBody = document.getElementById('serviceTableBody');
      
      // Giả lập dữ liệu dịch vụ cho mỗi tòa nhà
      const buildingServices = {
        '01': [
          { id: '1', name: 'Tiền phòng', unit: 'tháng', price: 1000000 },
          { id: '2', name: 'Tiền điện', unit: 'kWh', price: 3000 },
          { id: '3', name: 'Tiền nước', unit: 'm³', price: 10000 },
        ],
        '02': [
          { id: '1', name: 'Tiền phòng', unit: 'tháng', price: 1200000 },
          { id: '2', name: 'Tiền điện', unit: 'kWh', price: 3500 },
          { id: '3', name: 'Tiền nước', unit: 'm³', price: 12000 },
          { id: '4', name: 'Internet', unit: 'tháng', price: 100000 },
        ],
      };
      
      buildingSelect.addEventListener('change', function() {
        if (this.value) {
          serviceSelect.style.display = 'block';
          serviceTable.style.display = 'table';
          
          // Populate service dropdown
          serviceSelect.innerHTML = '<option value="">Chọn dịch vụ</option>';
          buildingServices[this.value].forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.name;
            serviceSelect.appendChild(option);
          });
          
          // Display all services for the selected building
          displayServices(this.value);
        } else {
          serviceSelect.style.display = 'none';
          serviceTable.style.display = 'none';
        }
      });
      
      serviceSelect.addEventListener('change', function() {
        if (this.value) {
          const buildingId = buildingSelect.value;
          const serviceId = this.value;
          const service = buildingServices[buildingId].find(s => s.id === serviceId);
          
          serviceTableBody.innerHTML = `
            <tr>
              <td>${service.name}</td>
              <td>${service.unit}</td>
              <td>${service.price.toLocaleString()} VND</td>
              <td>
                <button onclick="editService('${buildingId}', '${service.id}')">Sửa</button>
                <button onclick="deleteService('${buildingId}', '${service.id}')">Xóa</button>
              </td>
            </tr>
          `;
        } else {
          displayServices(buildingSelect.value);
        }
      });
      
      function displayServices(buildingId) {
        serviceTableBody.innerHTML = buildingServices[buildingId].map(service => `
          <tr>
            <td>${service.name}</td>
            <td>${service.unit}</td>
            <td>${service.price.toLocaleString()} VND</td>
            <td>
              <button onclick="editService('${buildingId}', '${service.id}')">Sửa</button>
              <button onclick="deleteService('${buildingId}', '${service.id}')">Xóa</button>
            </td>
          </tr>
        `).join('');
      }
      
      function editService(buildingId, serviceId) {
        // Implement edit functionality
        console.log(`Editing service ${serviceId} for building ${buildingId}`);
      }
      
      function deleteService(buildingId, serviceId) {
        // Implement delete functionality
        console.log(`Deleting service ${serviceId} for building ${buildingId}`);
      }
    </script>
  </body>
</html>