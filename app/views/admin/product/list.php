<?php include 'app/views/shares/header.php'; ?>

<div class="page-title text-center mb-4">
    <h1>Quản lý sản phẩm - Admin</h1>
    <p>Thêm, sửa, xóa sản phẩm</p>
</div>

<div class="text-end mb-4">
    <a href="/Admin/product/add" class="btn btn-primary btn-add-product">
        + Thêm sản phẩm mới
    </a>
</div>

<div id="product-list" class="product-grid">
    <!-- Sản phẩm sẽ được tải từ API tại đây -->
</div>

<div id="empty-box" class="empty-box" style="display: none;">
    Chưa có sản phẩm nào. Hãy bấm nút thêm sản phẩm để tạo sản phẩm mới.
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

    // Tải danh sách sản phẩm từ API
    fetch('/api/product', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => {
        if (response.status === 401) {
            localStorage.removeItem('jwtToken');
            localStorage.removeItem('userRole');
            location.href = '/Account/login';
            return;
        }
        return response.json();
    })
    .then(data => {
        const productList = document.getElementById('product-list');
        const emptyBox = document.getElementById('empty-box');
        
        if (!data || data.length === 0) {
            emptyBox.style.display = 'block';
            productList.style.display = 'none';
        } else {
            productList.style.display = 'grid';
            emptyBox.style.display = 'none';
            
            data.forEach((product, index) => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.innerHTML = `
                    <div class="product-image-box">
                        <div class="product-no-image">
                            Chưa có hình ảnh
                        </div>
                    </div>
                    <div class="product-card-top mt-3">
                        <span class="product-id">
                            STT: ${index + 1}
                        </span>
                        <span class="product-price">
                            ${new Intl.NumberFormat('vi-VN').format(product.price)} VNĐ
                        </span>
                    </div>
                    <h3 class="product-name">
                        ${product.name}
                    </h3>
                    <p class="product-description">
                        ${product.description}
                    </p>
                    <p class="product-description">
                        <strong>Danh mục:</strong>
                        ${product.category_name || 'Chưa có danh mục'}
                    </p>
                    <div class="product-actions">
                        <a href="/Product/show/${product.id}" class="btn-product btn-detail">
                            Chi tiết
                        </a>
                        <a href="/Admin/product/edit?id=${product.id}" class="btn-product btn-edit">
                            ✏️ Sửa
                        </a>
                        <button class="btn-product btn-delete" onclick="deleteProduct(${product.id})">
                            🗑️ Xóa
                        </button>
                    </div>
                `;
                productList.appendChild(productCard);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('empty-box').style.display = 'block';
        document.getElementById('empty-box').textContent = 'Có lỗi khi tải sản phẩm';
    });
});

function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        const token = localStorage.getItem('jwtToken');
        
        fetch(`/api/product/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product deleted successfully') {
                location.reload();
            } else {
                alert('Xóa sản phẩm thất bại');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra');
        });
    }
}
</script>
