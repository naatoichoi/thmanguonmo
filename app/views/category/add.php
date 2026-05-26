<?php include 'app/views/shares/header.php'; ?>

<div class="form-card">
    <div class="form-card-header">
        <h3>Thêm danh mục mới</h3>
        <p>Nhập thông tin danh mục sản phẩm</p>
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

        <form method="POST" action="/Category/save" onsubmit="return validateCategoryForm();">
            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Thêm danh mục
                </button>

                <a href="/Category/list" class="btn btn-outline-light">
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function validateCategoryForm() {
        let name = document.getElementById('name').value;
        let errors = [];

        if (name.trim() === '') {
            errors.push('Tên danh mục không được để trống.');
        }

        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }

        return true;
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>