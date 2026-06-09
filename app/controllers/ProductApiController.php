<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/utils/JWTHandler.php');

class ProductApiController
{
    private $productModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    private function authenticate()
    {
        $token = null;
        
        // Cách 1: Lấy từ apache_request_headers()
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
                $arr = explode(" ", $authHeader);
                $token = $arr[1] ?? null;
            }
        }
        
        // Cách 2: Nếu cách 1 không được, lấy từ $_SERVER
        if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            $arr = explode(" ", $authHeader);
            $token = $arr[1] ?? null;
        }
        
        // Cách 3: Thử getallheaders() (nếu có)
        if (!$token && function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
                $arr = explode(" ", $authHeader);
                $token = $arr[1] ?? null;
            }
        }
        
        if ($token) {
            $decoded = $this->jwtHandler->decode($token);
            return $decoded ? true : false;
        }
        
        return false;
    }

    // Lấy danh sách sản phẩm
    public function index()
    {
        if ($this->authenticate()) {
            header('Content-Type: application/json');
            $products = $this->productModel->getProducts();
            echo json_encode($products);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
        }
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

    // Thêm sản phẩm mới
    public function store()
    {
        header('Content-Type: application/json');
if (!$this->isAdmin()) {

    http_response_code(403);

    echo json_encode([
        'message' => 'Forbidden'
    ]);

    return;
}
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        $result = $this->productModel->addProduct($name, $description, $price, $category_id);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        }
    }

    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        header('Content-Type: application/json');
if (!$this->isAdmin()) {

    http_response_code(403);

    echo json_encode([
        'message' => 'Forbidden'
    ]);

    return;
}
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id);
        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden']);
            return;
        }

        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }
    private function getCurrentUser()
{
    $token = null;

    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $arr = explode(" ", $authHeader);
            $token = $arr[1] ?? null;
        }
    }

    if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $arr = explode(" ", $authHeader);
        $token = $arr[1] ?? null;
    }

    if (!$token && function_exists('getallheaders')) {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $arr = explode(" ", $authHeader);
            $token = $arr[1] ?? null;
        }
    }

    if (!$token) {
        return null;
    }

    return $this->jwtHandler->decode($token);
}
private function isAdmin()
{
    $user = $this->getCurrentUser();

    return $user
        && isset($user['role'])
        && $user['role'] === 'admin';
}
}
?>
