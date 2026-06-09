<?php include 'app/views/shares/header.php'; ?>

<div class="form-card">
    <div class="form-card-header">
        <h3>Sửa sản phẩm - Admin</h3>
        <p>Cập nhật thông tin sản phẩm</p>
    </div>

    <div class="form-card-body">
        <div id="error-message" class="alert alert-danger" style="display: none;"></div>

        <form id="edit-product-form">
            <input type="hidden" id="id" name="id">

            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Lưu thay đổi
                </button>

                <a href="/Admin/product/list" class="btn btn-outline-light">
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('jwtToken');
    const userRole = localStorage.getItem('userRole');
    
    if (!token || userRole !== 'admin') {
        alert('Bạn không có quyền truy cập trang này');
        location.href = '/Product/list';
        return;
    }

    // Lấy product ID từ URL query string
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (!productId) {
        alert('Lỗi: Không tìm thấy ID sản phẩm');
        location.href = '/Admin/product/list';
        return;
    }

    // Tải thông tin sản phẩm
    fetch(`/api/product/${productId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('id').value = data.id;
        document.getElementById('name').value = data.name;
        document.getElementById('description').value = data.description;
        document.getElementById('price').value = data.price;
        document.getElementById('category_id').value = data.category_id;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Không thể tải thông tin sản phẩm');
    });

    // Tải danh sách danh mục
    fetch('/api/category', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const categorySelect = document.getElementById('category_id');
        data.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            categorySelect.appendChild(option);
        });
    })
    .catch(error => console.error('Error:', error));

    // Xử lý form submit
    document.getElementById('edit-product-form').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const productId = document.getElementById('id').value;
        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch(`/api/product/${productId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product updated successfully') {
                location.href = '/Admin/product/list';
            } else if (data.errors) {
                document.getElementById('error-message').style.display = 'block';
                document.getElementById('error-message').innerHTML = Object.values(data.errors).join('<br>');
            } else {
                document.getElementById('error-message').style.display = 'block';
                document.getElementById('error-message').textContent = data.message || 'Có lỗi xảy ra';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('error-message').textContent = 'Có lỗi xảy ra';
        });
    });
});
</script>
