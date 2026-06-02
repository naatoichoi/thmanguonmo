<?php include 'app/views/shares/header.php'; ?>

<div class="page-title text-center mb-4">
    <h1>Danh sách danh mục</h1>
    <p>Quản lý các danh mục sản phẩm</p>
</div>

<div class="text-end mb-4">
    <a href="/Category/add" class="btn btn-primary rounded-pill px-4">+ Thêm danh mục</a>
    <a href="/Product/list" class="btn btn-outline-primary rounded-pill px-4 ms-2">Quay lại sản phẩm</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4 text-center">STT</th>
                    <th class="py-3">Tên danh mục</th>
                    <th class="py-3">Mô tả</th>
                    <th class="py-3 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="py-3 px-4 text-center align-middle"><?php echo $stt++; ?></td>
                        <td class="py-3 align-middle fw-bold">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="py-3 align-middle">
                            <?php echo htmlspecialchars($category->description ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="py-3 text-center align-middle">
                            <a href="/Category/edit/<?php echo $category->id; ?>" class="btn btn-primary btn-sm rounded-pill px-3">Sửa</a>
                            <a href="/Category/delete/<?php echo $category->id; ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>