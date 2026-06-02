<?php include 'app/views/shares/header.php'; ?>

<div class="page-title text-center mb-4">
    <h1>Danh sách sản phẩm</h1>
    <p>Quản lý sản phẩm</p>
</div>

<?php if (SessionHelper::isAdmin()): ?>
    <div class="text-end mb-4">
        <a href="/Product/add" class="btn btn-primary btn-add-product">
            + Thêm sản phẩm
        </a>
    </div>
<?php endif; ?>

<?php if (empty($products)): ?>
    <div class="empty-box">
        Chưa có sản phẩm nào. Hãy bấm nút thêm sản phẩm để tạo sản phẩm mới.
    </div>
<?php else: ?>
    <div class="product-grid">

        <?php $stt = 1; ?>

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <div class="product-image-box">

                    <?php if (!empty($product->image)): ?>

                        <img
                            src="/assets/images/products/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                            class="product-main-image">

                    <?php else: ?>

                        <div class="product-no-image">
                            Chưa có hình ảnh
                        </div>

                    <?php endif; ?>

                </div>

                <div class="product-card-top mt-3">

                    <span class="product-id">
                        STT: <?php echo $stt++; ?>
                    </span>

                    <span class="product-price">
                        <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
                    </span>

                </div>

                <h3 class="product-name">
                    <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                </h3>

                <p class="product-description">
                    <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <p class="product-description">
                    <strong>Danh mục:</strong>
                    <?php echo htmlspecialchars(
                        $product->category_name ?? 'Chưa có danh mục',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </p>

                <div class="product-actions">

                    <a href="/Cart/add/<?php echo $product->id; ?>"
                       class="btn-product btn-cart">
                        🛒 Thêm vào giỏ
                    </a>

                    <a href="/Product/show/<?php echo $product->id; ?>"
                       class="btn-product btn-detail">
                        Chi tiết
                    </a>

                    <?php if (SessionHelper::isAdmin()): ?>
                        <a href="/Product/edit/<?php echo $product->id; ?>"
                           class="btn-product btn-edit">
                            Sửa
                        </a>

                        <a href="/Product/delete/<?php echo $product->id; ?>"
                           class="btn-product btn-delete"
                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                            Xóa
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>
<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>