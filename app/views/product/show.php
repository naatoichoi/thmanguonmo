<?php include 'app/views/shares/header.php'; ?>

<div class="product-detail-card">
    <div class="row g-4">
        <div class="col-md-5">
            <div class="detail-main-image-box">
                <?php if (!empty($product->image)): ?>
                    <img id="mainProductImage"
                         src="/assets/images/products/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                         class="detail-main-image">
                <?php else: ?>
                    <div id="mainProductImagePlaceholder" class="product-no-image detail-no-image">
                        Chưa có hình ảnh chính
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-7">
            <span class="product-id">
                Mã: <?php echo htmlspecialchars($product->id, ENT_QUOTES, 'UTF-8'); ?>
            </span>

            <h1 class="detail-product-name mt-3">
                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
            </h1>

            <div class="detail-product-price">
                <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
            </div>

            <p class="detail-product-description">
                <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <p class="detail-category">
                <strong>Danh mục:</strong>
                <?php echo htmlspecialchars($product->category_name ?? 'Chưa có danh mục', ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <div class="d-flex gap-2 mt-4">

                <button type="button" onclick="addToCart(<?php echo $product->id; ?>)"
                   class="btn btn-success">
                    Thêm vào giỏ hàng
                </button>

                <div id="admin-edit-btn" style="display: none;">
                    <a href="/Admin/product/edit?id=<?php echo $product->id; ?>"
                       class="btn btn-primary">
                        Sửa sản phẩm
                    </a>
                </div>

                <?php if (SessionHelper::isAdmin()): ?>
                    <a href="/Product/edit/<?php echo $product->id; ?>"
                       class="btn btn-primary">
                        Sửa sản phẩm
                    </a>
                <?php endif; ?>

                <a href="/Product/list"
                   class="btn btn-outline-light">
                    Quay lại danh sách
                </a>

            </div>
        </div>
    </div>
</div>

<div class="product-detail-card mt-4">
    <h3 class="section-title">Hình ảnh khác của sản phẩm</h3>

    <?php if (empty($images)): ?>
        <div class="empty-box">
            Sản phẩm này chưa có hình ảnh phụ.
        </div>
    <?php else: ?>
        <div class="detail-image-grid">
            <?php foreach ($images as $image): ?>
                <div class="detail-sub-image-box">
                    <img src="/assets/images/products/<?php echo htmlspecialchars($image->image, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="Ảnh phụ sản phẩm"
                         class="detail-sub-image thumbnail-image"
                         onclick="swapMainImage(this)">
                </div>
            <?php endforeach; ?> 
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const userRole = localStorage.getItem('userRole');
        if (userRole === 'admin') {
            document.getElementById('admin-edit-btn').style.display = 'block';
        }
    });

    function swapMainImage(thumbnail) {
        let mainImage = document.getElementById('mainProductImage');
        let mainPlaceholder = document.getElementById('mainProductImagePlaceholder');

        if (mainImage) {
            let oldMainSrc = mainImage.src;
            let oldMainAlt = mainImage.alt;

            mainImage.src = thumbnail.src;
            mainImage.alt = thumbnail.alt;

            thumbnail.src = oldMainSrc;
            thumbnail.alt = oldMainAlt;
        } else if (mainPlaceholder) {
            let newMainImage = document.createElement('img');
            newMainImage.id = 'mainProductImage';
            newMainImage.src = thumbnail.src;
            newMainImage.alt = thumbnail.alt;
            newMainImage.className = 'detail-main-image';

            mainPlaceholder.replaceWith(newMainImage);
            thumbnail.parentElement.remove();
        }

        let thumbnails = document.querySelectorAll('.detail-sub-image-box');
        thumbnails.forEach(function (item) {
            item.classList.remove('active-thumbnail');
        });

        if (thumbnail.parentElement) {
            thumbnail.parentElement.classList.add('active-thumbnail');
        }
    }

    function addToCart(productId) {
        const token = localStorage.getItem('jwtToken');
        
        if (!token) {
            alert('Vui lòng đăng nhập để thêm vào giỏ');
            location.href = '/Account/login';
            return;
        }
        
        // Gửi request với JWT token
        fetch(`/Cart/add/${productId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => {
            if (response.status === 401) {
                alert('Bạn cần đăng nhập');
                location.href = '/Account/login';
                return;
            }
            if (response.status === 404) {
                alert('Sản phẩm không tồn tại');
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                alert('Đã thêm sản phẩm vào giỏ hàng');
                location.href = '/Cart/index';
            } else {
                alert('Có lỗi khi thêm vào giỏ hàng: ' + (data?.message || ''));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra');
        });
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>