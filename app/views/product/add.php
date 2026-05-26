<?php include 'app/views/shares/header.php'; ?>

<div class="form-card">
    <div class="form-card-header">
        <h3>Thêm sản phẩm mới</h3>
        <p>Nhập thông tin sản phẩm và hình ảnh</p>
    </div>

    <div class="form-card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/Product/save" enctype="multipart/form-data" onsubmit="return validateForm();">
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
                <input type="number" id="price" name="price" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>

                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>

            <div class="mb-4">
                <label for="images" class="form-label">Ảnh phụ sản phẩm</label>
                <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                <small class="text-muted">Có thể chọn nhiều ảnh cùng lúc.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Thêm sản phẩm
                </button>

                <a href="/Product/list" class="btn btn-outline-light">
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function validateForm() {
        let name = document.getElementById('name').value;
        let description = document.getElementById('description').value;
        let price = document.getElementById('price').value;
        let category = document.getElementById('category_id').value;
        let errors = [];

        if (name.trim() === '') {
            errors.push('Tên sản phẩm không được để trống.');
        }

        if (description.trim() === '') {
            errors.push('Mô tả không được để trống.');
        }

        if (price <= 0 || isNaN(price)) {
            errors.push('Giá phải là một số dương lớn hơn 0.');
        }

        if (category === '') {
            errors.push('Vui lòng chọn danh mục.');
        }

        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }

        return true;
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>