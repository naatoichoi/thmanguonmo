<?php include 'app/views/shares/header.php'; ?>

<div class="form-card">
    <div class="form-card-header">
        <h3>Sửa danh mục</h3>
        <p>Cập nhật thông tin danh mục sản phẩm</p>
    </div>

    <div class="form-card-body">
        <form method="POST" action="/Category/update" onsubmit="return validateCategoryForm();">
            <input type="hidden" name="id" value="<?php echo $category->id; ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục</label>
                <input type="text" id="name" name="name"
                       class="form-control"
                       value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>"
                       required>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Mô tả</label>
                <textarea id="description" name="description"
                          class="form-control"
                          rows="4"><?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Lưu thay đổi
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