<!DOCTYPE html>
<html>
<head>
    <title>Web bán hàng</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css?v=18" rel="stylesheet">
</head>
<body>
    <header class="site-header compact-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="/Product/list">Web bán hàng</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/Product/list">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Product/list">Sản phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Cart/index">Giỏ hàng</a>
                        </li>

                        <?php require_once 'app/helpers/SessionHelper.php'; ?>

                        <?php if(SessionHelper::isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/Category/list">Danh mục</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-primary fw-bold" href="/Product/add">Thêm sản phẩm</a>
                            </li>
                        <?php endif; ?>

                        <?php if(SessionHelper::isLoggedIn()): ?>
                            <li class="nav-item">
                                <span class="nav-link fw-bold text-success">
                                    Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                                    (<?php echo SessionHelper::getRole(); ?>)
                                </span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger fw-bold" href="/Account/logout">Đăng xuất</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item" id="nav-login">
                                <a class="nav-link" href="/Account/login">Đăng nhập</a>
                            </li>
                            <li class="nav-item" id="nav-register">
                                <a class="nav-link" href="/Account/register">Đăng ký</a>
                            </li>
                            <li class="nav-item" id="nav-jwt-admin" style="display: none;">
                                <a class="nav-link text-warning fw-bold" href="/Admin/product/list">👨‍💼 Admin</a>
                            </li>
                            <li class="nav-item" id="nav-jwt-user" style="display: none;">
                                <span class="nav-link fw-bold text-success" id="jwt-username"></span>
                            </li>
                            <li class="nav-item" id="nav-jwt-logout" style="display: none;">
                                <a class="nav-link text-danger fw-bold" href="#" onclick="logoutJWT(); return false;">Đăng xuất</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container mt-5 mb-5">

<script>
function logoutJWT() {
    localStorage.removeItem('jwtToken');
    localStorage.removeItem('userRole');
    location.href = '/Product/list';
}

document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('jwtToken');
    const userRole = localStorage.getItem('userRole');
    const sessionUser = '<?php echo SessionHelper::isLoggedIn() ? "true" : "false"; ?>';
    
    // Nếu không có session user nhưng có JWT token, hiển thị JWT controls
    if (token && sessionUser !== 'true') {
        document.getElementById('nav-login').style.display = 'none';
        document.getElementById('nav-register').style.display = 'none';
        document.getElementById('nav-jwt-user').style.display = 'block';
        document.getElementById('nav-jwt-logout').style.display = 'block';
        
        // Hiển thị admin link nếu user là admin
        if (userRole === 'admin') {
            document.getElementById('nav-jwt-admin').style.display = 'block';
        }
        
        // Thử decode và hiển thị username từ token
        try {
            const parts = token.split('.');
            const decoded = JSON.parse(atob(parts[1]));
            document.getElementById('jwt-username').textContent = 'Xin chào, ' + (decoded.username || 'Người dùng') + ' (' + userRole + ')';
        } catch(e) {
            document.getElementById('jwt-username').textContent = 'Xin chào, Người dùng (' + userRole + ')';
        }
    }
});
</script>