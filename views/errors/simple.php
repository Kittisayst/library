<?php
/**
 * @var string $type ປະເພດຂໍ້ຜິດພາດ
 * @var string $message ຂໍ້ຄວາມຂໍ້ຜິດພາດ
 */
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= htmlspecialchars($type) ?></title>
    <style>
        :root {
            --primary-color: #e74c3c;
            --text-color: #2c3e50;
            --background-color: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Lao', sans-serif;
            line-height: 1.6;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 3rem 2rem;
            animation: fadeIn 0.5s ease-in;
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

        .error-icon {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .error-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--text-color);
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .error-help {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .home-button {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .home-button:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">
            <?php
            switch ($type) {
                case '404':
                    echo 'ບໍ່ພົບໜ້າທີ່ຄົ້ນຫາ';
                    break;
                case '403':
                    echo 'ບໍ່ມີສິດເຂົ້າເຖິງ';
                    break;
                case '500':
                    echo 'ເກີດຂໍ້ຜິດພາດໃນລະບົບ';
                    break;
                default:
                    echo 'ເກີດຂໍ້ຜິດພາດ';
            }
            ?>
        </h1>
        
        <div class="error-message">
            <?= htmlspecialchars($message) ?>
        </div>

        <div class="error-help">
            <?php
            switch ($type) {
                case '404':
                    echo 'ກະລຸນາກວດສອບ URL ຫຼື ກັບໄປໜ້າຫຼັກ';
                    break;
                case '403':
                    echo 'ກະລຸນາເຂົ້າສູ່ລະບົບ ຫຼື ຕິດຕໍ່ຜູ້ດູແລລະບົບ';
                    break;
                default:
                    echo 'ກະລຸນາລອງໃໝ່ອີກຄັ້ງ ຫຼື ຕິດຕໍ່ຜູ້ດູແລລະບົບ';
            }
            ?>
        </div>

        <a href="<?= BASE_URL ?>" class="home-button">ກັບຄືນໜ້າຫຼັກ</a>
    </div>
</body>
</html>