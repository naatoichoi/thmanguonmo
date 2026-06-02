<?php

require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php'; 

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    private function isAdmin() {
        return SessionHelper::isAdmin();
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền truy cập trang quản lý danh mục!'); window.location.href='/Product/list';</script>";
            exit;
        }

        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    public function add()
    {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền thêm danh mục!'); window.location.href='/Product/list';</script>";
            exit;
        }

        include 'app/views/category/add.php';
    }

    public function save()
    {
        if (!$this->isAdmin()) {
            exit('Bạn không có quyền thực hiện thao tác này!');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $result = $this->categoryModel->addCategory($name, $description);

            if (is_array($result)) {
                $errors = $result;
                include 'app/views/category/add.php';
                return;
            }

            if ($result) {
                header('Location: /Category/list');
                exit();
            }

            echo "Đã xảy ra lỗi khi thêm danh mục.";
        }
    }

    public function edit($id)
    {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền sửa danh mục!'); window.location.href='/Product/list';</script>";
            exit;
        }

        $category = $this->categoryModel->getCategoryById($id);

        if ($category) {
            include 'app/views/category/edit.php';
        } else {
            echo "Không tìm thấy danh mục.";
        }
    }

    public function update()
    {
        if (!$this->isAdmin()) {
            exit('Bạn không có quyền thực hiện thao tác này!');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $edit = $this->categoryModel->updateCategory($id, $name, $description);

            if ($edit) {
                header('Location: /Category/list');
                exit();
            }

            echo "Đã xảy ra lỗi khi sửa danh mục.";
        }
    }

    public function delete($id)
    {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền xóa danh mục!'); window.location.href='/Product/list';</script>";
            exit;
        }

        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: /Category/list');
            exit();
        }

        echo "Đã xảy ra lỗi khi xóa danh mục.";
    }
}
?>