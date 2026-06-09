<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/helpers/SessionHelper.php');
require_once('app/utils/JWTHandler.php');

class AccountController {
    private $accountModel;
    private $db;
    private $jwtHandler;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    public function register() { include_once 'app/views/account/register.php'; }
    public function login() { include_once 'app/views/account/login.php'; }

    public function save(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $errors = [];

            if(empty($username)){
                $errors['username'] = "Vui lòng nhập tên đăng nhập!";
            }
            if(empty($fullName)){
                $errors['fullname'] = "Vui lòng nhập tên đầy đủ!";
            }
            if(empty($password)){
                $errors['password'] = "Vui lòng nhập mật khẩu!";
            }
            if($password != $confirmPassword){
                $errors['confirmPass'] = "Mật khẩu xác nhận không khớp!";
            }

            // Kiểm tra username đã được đăng ký chưa
            $account = $this->accountModel->getAccountByUsername($username);
            if($account){
                $errors['account'] = "Tài khoản này đã được đăng ký!";
            }

            if(count($errors) > 0){
                include_once 'app/views/account/register.php';
            }else{
                $result = $this->accountModel->save($username, $fullName, $password);
                if($result){
                    header('Location: /Account/login');
                    exit;
                } else {
                    $errors['save'] = "Lỗi khi lưu tài khoản!";
                    include_once 'app/views/account/register.php';
                }
            } 
        } 
    }

    private function getConfig($provider = 'github') {
        $path = realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR . 'config_oauth.php';
        if (!file_exists($path)) {
            die("LỖI: Không tìm thấy file cấu hình tại: " . $path);
        }
        $allConfig = require $path;
        return $allConfig[$provider] ?? die("LỖI: Không tìm thấy cấu hình cho $provider");
    }

    public function loginGitHub() {
        $config = $this->getConfig('github');
        $url = "https://github.com/login/oauth/authorize?client_id=" . $config['client_id'] . 
               "&redirect_uri=" . urlencode($config['redirect_uri']) . "&scope=user:email";
        header("Location: $url");
        exit;
    }

    public function githubCallback() {
        if (!isset($_GET['code'])) die("Lỗi xác thực GitHub!");
        $config = $this->getConfig('github');
        $postParams = ['client_id' => $config['client_id'], 'client_secret' => $config['client_secret'], 'code' => $_GET['code'], 'redirect_uri' => $config['redirect_uri']];

        $ch = curl_init("https://github.com/login/oauth/access_token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $tokenData = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $ch = curl_init("https://api.github.com/user");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: token ' . $tokenData['access_token'], 'User-Agent: WebBanHangPhamNgocAnh']);
        $githubUser = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $this->loginOrRegister('gh_' . $githubUser['login'], $githubUser['name'] ?? $githubUser['login']);
    }

    // --- ĐĂNG NHẬP GOOGLE ---
    public function loginGoogle() {
        $config = $this->getConfig('google');
        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $config['client_id'], 'redirect_uri' => $config['redirect_uri'], 'response_type' => 'code', 'scope' => 'email profile', 'access_type' => 'online'
        ]);
        header("Location: $url");
        exit;
    }

    public function googleCallback() {
        if (!isset($_GET['code'])) die("Lỗi xác thực Google!");
        $config = $this->getConfig('google');
        $postParams = ['client_id' => $config['client_id'], 'client_secret' => $config['client_secret'], 'code' => $_GET['code'], 'redirect_uri' => $config['redirect_uri'], 'grant_type' => 'authorization_code'];

        $ch = curl_init("https://oauth2.googleapis.com/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
        $tokenData = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $ch = curl_init("https://www.googleapis.com/oauth2/v3/userinfo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenData['access_token']]);
        $googleUser = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $this->loginOrRegister('gg_' . $googleUser['email'], $googleUser['name'] ?? $googleUser['email']);
    }

    // --- HÀM HỖ TRỢ DÙNG CHUNG ---
    private function loginOrRegister($username, $displayName) {
        $account = $this->accountModel->getAccountByUsername($username);
        if (!$account) {
            $this->accountModel->save($username, $displayName, 'oauth_user', 'user');
            $account = $this->accountModel->getAccountByUsername($username);
        }
        SessionHelper::start();
        $_SESSION['user_id'] = $account->id;
        $_SESSION['username'] = $account->username;
        $_SESSION['role'] = $account->role;
        header('Location: /Product/list');
        exit;
    }
public function checkLogin() {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $user = $this->accountModel->getAccountByUsername($username);

    if ($user && password_verify($password, $user->password)) {

        SessionHelper::start();

        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role ?? 'user';

        $token = $this->jwtHandler->encode([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role ?? 'user'
        ]);

        echo json_encode([
            'token' => $token,
            'role' => $user->role ?? 'user'
        ]);

    } else {

        http_response_code(401);

        echo json_encode([
            'message' => 'Invalid credentials'
        ]);
    }
}

    public function logout() { SessionHelper::start(); session_destroy(); header('Location: /Product/list'); exit; }
}