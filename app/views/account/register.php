<?php include 'app/views/shares/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body p-5">
                <h3 class="text-center mb-4">Đăng ký tài khoản</h3>
                
                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo $err; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/Account/save" method="post">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Tên đăng nhập" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="fullname" placeholder="Họ và tên" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" class="form-control" name="confirmpassword" placeholder="Xác nhận mật khẩu" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng ký</button>
                    </div>
                    <div class="text-center mt-3">
                        Đã có tài khoản? <a href="/Account/login">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>