<?php include 'app/views/shares/header.php'; ?>

<div class="form-card">
    <div class="form-card-header">
        <h3>Thông tin đặt hàng</h3>
        <p>Vui lòng điền đầy đủ thông tin để chúng tôi giao hàng nhanh nhất</p>
    </div>

    <div class="form-card-body">
        <form method="POST" action="/Order/processCheckout">
            <div class="mb-3">
                <label for="name" class="form-label">Họ tên người nhận</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Ví dụ: Phạm Ngọc Anh" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Ví dụ: 0912345678" required>
            </div>

            <div class="mb-4">
                <label for="address" class="form-label">Địa chỉ nhận hàng</label>
                <textarea id="address" name="address" class="form-control" rows="3" placeholder="Ghi rõ số nhà, tên đường, xã/phường, quận/huyện..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Phương thức thanh toán</label>
                
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked>
                    <label class="form-check-label fw-bold text-success" for="pay_cod">Thanh toán tiền mặt (COD)</label>
                </div>
                
                <div class="form-check form-check-inline ms-3">
                    <input class="form-check-input" type="radio" name="payment_method" id="pay_vnpay" value="VNPAY">
                    <label class="form-check-label fw-bold text-primary" for="pay_vnpay">Thanh toán qua VNPAY</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Xác nhận đặt hàng
                </button>
                <a href="/Cart/index" class="btn btn-outline-light">
                    Quay lại giỏ hàng
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>