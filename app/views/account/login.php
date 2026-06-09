<?php include 'app/views/shares/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-body p-5 text-center">
                <h3 class="fw-bold mb-4">Đăng nhập</h3>
                
                <div id="error-message" class="alert alert-danger" style="display: none;"></div>

                <form id="login-form">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Tên đăng nhập" required />
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Mật khẩu" required />
                    </div>
                    
                    <button class="btn btn-primary btn-lg w-100" type="submit">Đăng nhập</button>
                </form>

                <div class="mt-4">
                    <hr>
                    <p class="text-muted mb-3">Hoặc đăng nhập với</p>
                    
                    <div class="d-grid gap-2">
                        <a href="/Account/loginGitHub" class="btn btn-dark btn-lg">
                            <i class="fab fa-github me-2"></i> GitHub
                        </a>
                        
                        <a href="/Account/loginGoogle" class="btn btn-outline-danger btn-lg">
                            <i class="fab fa-google me-2"></i> Google
                        </a>
                    </div>
                </div>
                
                <div class="mt-4">
                    <p class="mb-0">Chưa có tài khoản? <a href="/Account/register" class="fw-bold">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
<script>
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });

    fetch('/Account/checkLogin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            localStorage.setItem('jwtToken', data.token);
            localStorage.setItem('userRole', data.role || 'user');
            
            // Redirect dựa trên role
            if (data.role === 'admin') {
                location.href = '/Admin/product/list';
            } else {
                location.href = '/Product/list';
            }
        } else {
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('error-message').textContent = data.message || 'Đăng nhập thất bại';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('error-message').textContent = 'Có lỗi xảy ra';
    });
});
</script>