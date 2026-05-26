<?php include 'app/views/shares/header.php'; ?>

<div class="page-title text-center mb-4">
    <h1>Danh sách danh mục</h1>
    <p>Quản lý các danh mục sản phẩm</p>
</div>

<div class="d-flex justify-content-end gap-2 mb-4">
    <a href="/Category/add" class="btn btn-primary btn-add-product">
        + Thêm danh mục
    </a>

    <a href="/Product/list" class="btn btn-outline-light">
        Quay lại sản phẩm
    </a>
</div>

<?php if (empty($categories)): ?>
    <div class="empty-box">
        Chưa có danh mục nào. Hãy bấm nút thêm danh mục để tạo mới.
    </div>
<?php else: ?>
    <div class="table-card">
        <table class="table table-hover align-middle category-table mb-0">
            <thead>
                <tr>
                    <th style="width: 100px;">STT</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th style="width: 180px;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php $stt = 1; ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <span class="product-id">
                                <?php echo $stt++; ?>
                            </span>
                        </td>

                        <td class="fw-bold">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <div class="d-flex gap-2">
                                <a href="/Category/edit/<?php echo $category->id; ?>" class="btn btn-primary btn-sm">
                                    Sửa
                                </a>

                                <a href="/Category/delete/<?php echo $category->id; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    Xóa
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>