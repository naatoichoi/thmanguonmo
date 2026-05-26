<?php include 'app/views/shares/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Giỏ hàng</h2>

    <?php if (!empty($items)): ?>
        <a href="/Cart/clear"
           class="btn btn-danger"
           onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?');">
            Xóa toàn bộ giỏ hàng
        </a>
    <?php endif; ?>
</div>

<?php if (empty($items)): ?>

    <div class="empty-box">
        Giỏ hàng đang trống.
    </div>

<?php else: ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th width="180">Số lượng</th>
                    <th>Thành tiền</th>
                    <th width="120">Thao tác</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($items as $item): ?>

                <?php $product = $item['product']; ?>

                <tr>

                    <td width="120">
                        <?php if (!empty($product->image)): ?>
                            <img src="/assets/images/products/<?php echo htmlspecialchars($product->image); ?>"
                                 width="100"
                                 class="img-fluid rounded">
                        <?php else: ?>
                            <span>Chưa có ảnh</span>
                        <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($product->name); ?></td>

                    <td><?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ</td>

                    <td>
                        <form action="/Cart/update" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">

                            <div class="d-flex gap-2">
                                <input type="number"
                                       name="quantity"
                                       value="<?php echo $item['quantity']; ?>"
                                       min="1"
                                       class="form-control">

                                <button class="btn btn-primary">Lưu</button>
                            </div>
                        </form>
                    </td>

                    <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VNĐ</td>

                    <td>
                        <a href="/Cart/remove/<?php echo $product->id; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Xóa sản phẩm này?');">
                            Xóa
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>
    </div>

    <div class="text-end mt-4">

        <h4>
            Tổng tiền:
            <span class="text-success">
                <?php echo number_format($total, 0, ',', '.'); ?> VNĐ
            </span>
        </h4>

<!-- COD -->
<button class="btn btn-success mt-3" onclick="checkoutCOD()">
    Thanh toán COD
</button>

<!-- MOMO -->
<a href="/order/createMomo"
   class="btn btn-danger mt-3">
    Thanh toán MoMo
</a>

    </div>

    <script>
        function checkoutCOD() {
            fetch('/order/checkoutCOD')
                .then(res => res.text())
                .then(data => {

                    if (data === 'success') {
                        alert('🎉 Đặt hàng thành công!');
                        window.location.href = '/Cart/index';
                    } else if (data === 'empty') {
                        alert('Giỏ hàng trống!');
                    } else {
                        alert('Có lỗi xảy ra!');
                    }

                })
                .catch(() => alert('Lỗi hệ thống!'));
        }
    </script>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>