<?php
/**
 * @var string $type ປະເພດຂໍ້ຜິດພາດ
 * @var string $title ຫົວຂໍ້ຂໍ້ຜິດພາດ
 * @var string $message ຂໍ້ຄວາມຂໍ້ຜິດພາດ
 * @var mixed $details ລາຍລະອຽດເພີ່ມເຕີມ
 */
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        /* CSS ຈາກໜ້າ 404 ທີ່ສ້າງກ່ອນໜ້ານີ້ */
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?= htmlspecialchars($type) ?></div>
        <h1 class="error-message"><?= htmlspecialchars($title) ?></h1>

        <div class="error-details">
            <div class="error-item">
                <div class="error-type"><?= htmlspecialchars($message) ?></div>
                <?php if ($details && ENVIRONMENT === 'development'): ?>
                    <div class="error-path">
                        <?= nl2br(htmlspecialchars(print_r($details, true))) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="debug-info">
            <strong>URL ທີ່ຮ້ອງຂໍ:</strong>
            <div class="requested-url"><?= htmlspecialchars($url) ?></div>
            <?php if (ENVIRONMENT === 'development'): ?>
                <div class="timestamp">
                    <strong>ເວລາ:</strong> <?= htmlspecialchars($timestamp) ?>
                </div>
            <?php endif; ?>
        </div>

        <a href="<?= BASE_URL ?>" class="home-button">ກັບຄືນໜ້າຫຼັກ</a>
    </div>
</body>
</html>