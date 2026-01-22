<?php

class BaseController
{
    protected function view($viewPath, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . "/../../views/{$viewPath}.php";
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("View not found: {$viewPath}");
        }
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    protected function json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth()
    {
        if (!Session::isLoggedIn()) {
            $this->redirect('/');
        }
    }

    protected function requireSuperuser()
    {
        $this->requireAuth();
        if (!Session::isSuperuser()) {
            $this->redirect('/dashboard');
        }
    }

    protected function getCurrentUserId()
    {
        if (Session::has('user_id')) {
            return Session::get('user_id');
        }

        // Fallback for existing sessions
        $domain = Session::getDomain();
        if ($domain) {
            $userModel = new User();
            $user = $userModel->findByDomain($domain);
            if ($user) {
                Session::set('user_id', $user['id']);
                return $user['id'];
            }
        }

        return 0; // Unknown/System
    }
}
