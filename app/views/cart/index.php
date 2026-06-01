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
                    <th width="150">Số lượng</th>
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
                        <form action="/Cart/update" method="POST" id="form-update-<?php echo $product->id; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">

                            <div class="input-group input-group-sm" style="width: 120px;">
                                <button class="btn btn-outline-secondary fw-bold" type="button" 
                                        onclick="updateQty(<?php echo $product->id; ?>, -1)">-</button>
                                
                                <input type="text" name="quantity" id="qty-<?php echo $product->id; ?>" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       class="form-control text-center fw-bold" readonly>
                                
                                <button class="btn btn-outline-secondary fw-bold" type="button" 
                                        onclick="updateQty(<?php echo $product->id; ?>, 1)">+</button>
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
            <span class="text-success fw-bold">
                <?php echo number_format($total, 0, ',', '.'); ?> VNĐ
            </span>
        </h4>

        <a href="/Order/checkout" class="btn btn-primary mt-3 px-4 py-2 fw-bold">
            Tiến hành thanh toán
        </a>
    </div>

    <script>
        function updateQty(productId, change) {
            const input = document.getElementById('qty-' + productId);
            let currentQty = parseInt(input.value);
            let newQty = currentQty + change;

            if (newQty >= 1) {
                input.value = newQty;
                document.getElementById('form-update-' + productId).submit();
            }
        }
    </script>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>