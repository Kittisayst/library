<?php
class Controller
{
    /**
     * @var string Layout ທີ່ຈະໃຊ້
     */
    protected string $layout = 'main';

    /**
     * @var array ຂໍ້ມູນທີ່ຈະສົ່ງໄປໃຫ້ view
     */
    protected array $data = [];

    /**
     * Validation rules array
     * @var array
     */
    protected array $rules = [];

    /**
     * Validation errors
     * @var array
     */
    protected array $errors = [];

    /**
     * ຟັງຊັນສຳລັບການ render view
     * @param string $view ຊື່ view ທີ່ຕ້ອງການ render
     * @param array $data ຂໍ້ມູນທີ່ຈະສົ່ງໄປໃຫ້ view
     * @param string|null $layout ຊື່ layout ທີ່ຈະໃຊ້
     * @return void
     */
    protected function render(string $view, array $data = [], ?string $layout = null): void
    {
        $this->data = array_merge($this->data, $data);

        // ຄົ້ນຫາໄຟລ໌ view
        $viewFile = $this->findViewFile($view);

        // ຖ້າມີການກຳນົດ layout
        if ($layout !== null || $this->layout !== '') {
            $layoutFile = $this->findLayoutFile($layout ?? $this->layout);

            // ເລີ່ມ output buffering ສຳລັບ view
            ob_start();
            $this->renderPhpFile($viewFile, $this->data);
            $content = ob_get_clean();

            // Render layout ພ້ອມກັບເນື້ອຫາຂອງ view
            $this->renderPhpFile($layoutFile, array_merge($this->data, ['content' => $content]));
        } else {
            // Render ສະເພາະ view
            $this->renderPhpFile($viewFile, $this->data);
        }
    }

    protected function renderPartial(string $view, array $data = []): string
    {
        ob_start();
        $this->renderPhpFile($this->findViewFile($view), $data);
        return ob_get_clean();
    }

    /**
     * ຄົ້ນຫາໄຟລ໌ view
     * @param string $view
     * @return string
     */
    protected function findViewFile(string $view): string
    {
        $viewFile = "views/{$view}.php";
        if (!is_file($viewFile)) {
            throw new RuntimeException("View file not found: {$viewFile}");
        }
        return $viewFile;
    }

    /**
     * ຄົ້ນຫາໄຟລ໌ layout
     * @param string $layout
     * @return string
     */
    protected function findLayoutFile(string $layout): string
    {
        $layoutFile = "views/layouts/{$layout}.php";
        if (!is_file($layoutFile)) {
            die("Layout file not found: {$layoutFile}");
        }
        return $layoutFile;
    }

    /**
     * Render PHP file
     * @param string $file
     * @param array $data
     */
    protected function renderPhpFile(string $file, array $data = []): void
    {
        if (!str_starts_with(realpath($file), realpath('views/'))) {
            throw new RuntimeException('Invalid file path');
        }
        extract($data, EXTR_SKIP);
        include $file;
    }

    /**
     * ປິດການໃຊ້ງານ layout
     */
    protected function disableLayout(): void
    {
        $this->layout = '';
    }

    /**
     * ສົ່ງຄືນຂໍ້ມູນແບບ JSON
     * @param mixed $data
     */
    protected function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // ເພີ່ມຟັງຊັນສຳລັບສ້າງ CSRF token
    protected function generateCsrfToken(): string
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(
                openssl_encrypt(
                    random_bytes(32),
                    'AES-256-CBC',
                    ENCRYPTION_KEY,
                    0,
                    str_repeat('0', 16)
                )
            );
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }



    // ເພີ່ມຟັງຊັນກວດສອບ CSRF token
    protected function validateCsrfToken(?string $token): bool
    {
        return isset($_SESSION[CSRF_TOKEN_NAME]) &&
            hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    /**
     * ສ້າງ CSRF input field
     * @return string HTML input
     */
    protected function useCsrf(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            CSRF_TOKEN_NAME,
            htmlspecialchars(self::generateCsrfToken())
        );
    }

    protected function isCsrfToken(): bool
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? null;
        if (!$token || !self::validateCsrfToken($token)) {
            throw new RuntimeException('Invalid CSRF token');
        }
        return true;
    }

    protected function error(string $message): void
    {
        $errorController = new ErrorController();
        $errorController->handle('403', [
            'message' => $message
        ]);
        exit();
    }

    /**
     * Validate input data
     * @param array $data
     * @return bool
     */
    protected function validate(array $data): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                if (is_string($rule)) {
                    $params = [];
                    if (str_contains($rule, ':')) {
                        [$rule, $param] = explode(':', $rule);
                        $params = explode(',', $param);
                    }

                    $value = $data[$field] ?? null;

                    switch ($rule) {
                        case 'required':
                            if (empty($value)) {
                                $this->errors[$field][] = "Field {$field} is required";
                            }
                            break;

                        case 'email':
                            if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->errors[$field][] = "Field {$field} must be valid email";
                            }
                            break;

                        case 'min':
                            if (strlen($value) < (int)$params[0]) {
                                $this->errors[$field][] = "Field {$field} must be at least {$params[0]} characters";
                            }
                            break;

                        case 'max':
                            if (strlen($value) > (int)$params[0]) {
                                $this->errors[$field][] = "Field {$field} must not exceed {$params[0]} characters";
                            }
                            break;

                        case 'numeric':
                            if ($value && !is_numeric($value)) {
                                $this->errors[$field][] = "Field {$field} must be numeric";
                            }
                            break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    protected function setViewData(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    protected function getViewData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    protected function registerJs(string $path): void
    {
        $this->data['js'][] = "public/js/".$path;
    }

    protected function registerCss(string $path): void
    {
        $this->data['css'][] = "public/css/".$path;
    }

    protected function setMetaTags(array $tags): void
    {
        $this->data['metaTags'] = array_merge($this->data['metaTags'] ?? [], $tags);
    }

    /**
     * Set validation rules
     * @param array $rules ['field' => ['required', 'email', 'min:8']]
     */
    protected function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * Get validation errors
     * @return array
     */
    protected function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Redirect to specified path
     * @param string $path
     * @param array $params Query parameters
     */
    protected function redirect(string $path, array $params = []): void
    {
        if (!empty($_POST)) {
            $this->flashOldInput($_POST);
        }
        $url = $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        header('Location: ' . BASE_URL . trim($url, '/'));
        exit();
    }

    /**
     * Redirect back to previous page
     * @param array $params Query parameters
     */
    protected function redirectBack(array $params = []): void
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($url, $params);
    }

    /**
     * Redirect with flash message
     * @param string $path
     * @param string $message
     * @param string $type success|error|info|warning
     */
    protected function redirectWith(string $path, string $message, string $type = 'info'): void
    {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
        $this->redirect($path);
    }

    /**
     * Flash old input data to session
     * @param array $data
     */
    protected function flashOldInput(array $data): void
    {
        $_SESSION['_old_input'] = array_filter(
            $data,
            fn($value) =>
            !is_array($value) && !is_object($value)
        );
    }

    protected function old(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }

    /**
     * Clear old input data
     */
    protected function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }
}
