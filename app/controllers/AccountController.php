<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/helpers/SessionHelper.php');

class AccountController {
    private $accountModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

    public function register() { include_once 'app/views/account/register.php'; }
    public function login() { include_once 'app/views/account/login.php'; }

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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $account = $this->accountModel->getAccountByUsername($username);

            if ($account && password_verify($password, $account->password)) {
                SessionHelper::start();
                $_SESSION['user_id'] = $account->id; 
                $_SESSION['username'] = $account->username;
                $_SESSION['role'] = $account->role;
                header('Location: /Product/list');
                exit;
            } else {
                $error = $account ? "Mật khẩu không đúng!" : "Không tìm thấy tài khoản!";
                include_once 'app/views/account/login.php';
                exit;
            }
        }
    }

    public function logout() { SessionHelper::start(); session_destroy(); header('Location: /Product/list'); exit; }
}