<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - ບໍ່ພົບໜ້າທີ່ຄົ້ນຫາ</title>
    <style>
        :root {
            --primary-color: #4a90e2;
            --error-color: #e74c3c;
            --text-color: #333;
            --background-color: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .error-container {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-in;
        }

        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-message {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #666;
        }

        .error-details {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .error-details h2 {
            color: var(--error-color);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .error-item {
            background-color: #feebeb;
            border-left: 4px solid var(--error-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 4px 4px 0;
        }

        .error-item:last-child {
            margin-bottom: 0;
        }

        .error-type {
            font-weight: bold;
            color: var(--error-color);
            margin-bottom: 0.5rem;
        }

        .error-path {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            word-break: break-all;
        }

        .home-button {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        .home-button:hover {
            background-color: #357abd;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 4rem;
            }

            .error-message {
                font-size: 1.2rem;
            }

            .error-details {
                padding: 1rem;
            }
        }

        .debug-info {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #666;
        }

        .requested-url {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">ບໍ່ພົບໜ້າທີ່ທ່ານຄົ້ນຫາ</h1>

        <div class="error-details">
            <h2>ລາຍລະອຽດຂໍ້ຜິດພາດ:</h2>
            <?php
            // ຮັບຂໍ້ຄວາມຂໍ້ຜິດພາດຈາກ URL parameter
            $errorType = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
            $controller = isset($_GET['controller']) ? htmlspecialchars($_GET['controller']) : '';
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

            switch($errorType) {
                case 'controller_not_found':
                    echo "<div class='error-item'>
                            <div class='error-type'>ບໍ່ພົບ Controller</div>
                            <div>Controller not found: {$controller}</div>
                            <div class='error-path'>Path: controllers/{$controller}.php</div>
                          </div>";
                    break;
                    
                case 'class_not_found':
                    echo "<div class='error-item'>
                            <div class='error-type'>ບໍ່ພົບ Controller Class</div>
                            <div>Controller class not found: {$controller}</div>
                            <div class='error-path'>Class Name: {$controller}</div>
                          </div>";
                    break;
                    
                case 'action_not_found':
                    echo "<div class='error-item'>
                            <div class='error-type'>ບໍ່ພົບ Action Method</div>
                            <div>Action not found: {$action} in {$controller}</div>
                            <div class='error-path'>Method: {$action}()</div>
                          </div>";
                    break;
                    
                default:
                    echo "<div class='error-item'>
                            <div class='error-type'>ບໍ່ພົບໜ້າທີ່ຮ້ອງຂໍ</div>
                            <div>ໜ້າທີ່ທ່ານພະຍາຍາມເຂົ້າເຖິງບໍ່ມີຢູ່ໃນລະບົບ</div>
                          </div>";
            }
            ?>
        </div>

        <div class="debug-info">
            <strong>URL ທີ່ຮ້ອງຂໍ:</strong>
            <div class="requested-url"><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></div>
        </div>

        <a href="<?php echo '/jcmvc/'; ?>" class="home-button">ກັບຄືນໜ້າຫຼັກ</a>
    </div>

    <?php
    // ບັນທຶກ 404 errors
    $logMessage = date('Y-m-d H:i:s') . " | 404 Error | " .
                 "Type: " . $errorType . " | " .
                 "Controller: " . $controller . " | " .
                 "Action: " . $action . " | " .
                 "URL: " . $_SERVER['REQUEST_URI'] . " | " .
                 "Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'Direct Access') . " | " .
                 "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    error_log($logMessage, 3, "logs/404_errors.log");
    ?>
</body>
</html>