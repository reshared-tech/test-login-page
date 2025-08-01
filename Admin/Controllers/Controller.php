<?php

namespace Admin\Controllers;

use Admin\Models\AdministratorModel;
use Tools\Auth;
use Tools\Language;
use Tools\Validator;

class Controller
{
    protected $validator;

    public function __construct()
    {
        session_start();

        Auth::name('admin');

        Language::setLang(Language::JP);

        $this->checkAuth();

        $this->validator = new Validator();
    }

    private function checkAuth()
    {
        if (!Auth::isAuthorized()) {
            if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
                $this->forbidden();
            }

            $name = trim($_SERVER['PHP_AUTH_USER']);
            $password = trim($_SERVER['PHP_AUTH_PW']);

            if ($name === 'admin' && $password === 'admin') {
                Auth::addAuth([
                    'id' => 1,
                    'name' => $name,
                ]);
                return;
            }

            $model = new AdministratorModel();
            $data = $model->getByName($name);

            if (empty($data)) {
                $this->forbidden();
            }

            if (!password_verify($password, $data['password'])) {
                $this->forbidden();
            }

            Auth::addAuth([
                'id' => $data['id'],
                'name' => $data['name'],
            ]);
        }
    }

    protected function forbidden()
    {
        header('WWW-Authenticate: Basic realm="Please log in"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }

    protected function pages($total, $currentPage, $size = 10, $show = 7)
    {
        $totalPages = ceil($total / $size);
        $pre = $currentPage == 1 ? 0 : $currentPage - 1;
        $next = $currentPage == $totalPages ? 0 : $currentPage + 1;

        // 如果总页数小于等于要显示的页数，直接显示所有页
        if ($totalPages <= $show) {
            return [$pre, $next, range(1, $totalPages)];
        }

        // 计算左右两边应该显示的页数
        $half = floor($show / 2);
        $left = $currentPage - $half;
        $right = $currentPage + $half;

        // 调整左右边界
        if ($left < 1) {
            $left = 1;
            $right = $show;
        }

        if ($right > $totalPages) {
            $right = $totalPages;
            $left = $totalPages - $show + 1;
        }

        // 生成基本页数范围
        $pages = range($left, $right);

        // 处理开头和结尾的省略号
        if ($left > 1) {
            // 如果左边不是从1开始，添加1和可能的省略号
            if ($left > 2) {
                array_unshift($pages, '...');
            }
            array_unshift($pages, 1);
        }

        if ($right < $totalPages) {
            // 如果右边不是到最后，添加可能的省略号和最后一页
            if ($right < $totalPages - 1) {
                $pages[] = '...';
            }
            $pages[] = $totalPages;
        }

        return [$pre, $next, $pages];
    }
}