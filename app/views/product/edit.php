<?php include 'app/views/shares/header.php'; ?>

<div class="form-card">
    <div class="form-card-header">
        <h3>Sửa sản phẩm</h3>
        <p>Cập nhật thông tin sản phẩm và hình ảnh</p>
    </div>

    <div class="form-card-body">
        <form method="POST" action="/Product/update" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product->id; ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" id="name" name="name"
                       class="form-control"
                       value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea id="description" name="description"
                          class="form-control"
                          rows="4"
                          required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" id="price" name="price"
                       class="form-control"
                       step="0.01"
                       value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>

                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>"
                            <?php echo ($category->id == $product->category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ảnh chính hiện tại</label>

                <?php if (!empty($product->image)): ?>
                    <div class="edit-current-image mb-2">
                        <img src="/assets/images/products/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                             alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                <?php else: ?>
                    <div class="empty-box mb-2">
                        Sản phẩm chưa có ảnh chính.
                    </div>
                <?php endif; ?>

                <label for="image" class="form-label">Thay ảnh chính mới</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Nếu không chọn ảnh mới, ảnh chính cũ sẽ được giữ nguyên.</small>
            </div>

            <div class="mb-4">
                <label for="images" class="form-label">Thêm ảnh phụ mới</label>
                <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                <small class="text-muted">Có thể chọn nhiều ảnh cùng lúc. Ảnh mới sẽ được thêm vào danh sách ảnh cũ.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Lưu thay đổi
                </button>

                <a href="/Product/show/<?php echo $product->id; ?>" class="btn btn-outline-light">
                    Xem chi tiết
                </a>

                <a href="/Product/list" class="btn btn-outline-light">
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>

<div class="product-detail-card mt-4">
    <h3 class="section-title">Ảnh phụ hiện tại</h3>

    <?php if (empty($images)): ?>
        <div class="empty-box">
            Sản phẩm này chưa có ảnh phụ.
        </div>
    <?php else: ?>
        <div class="detail-image-grid">
            <?php foreach ($images as $image): ?>
                <div class="detail-sub-image-box image-manage-box">
                    <img src="/assets/images/products/<?php echo htmlspecialchars($image->image, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="Ảnh phụ sản phẩm"
                         class="detail-sub-image">

                    <a href="/Product/deleteImage/<?php echo $image->id; ?>/<?php echo $product->id; ?>"
                       class="btn btn-danger btn-sm image-delete-btn"
                       onclick="return confirm('Bạn có chắc chắn muốn xóa ảnh này?');">
                        Xóa ảnh
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>