<?php
/**
 * @var string $type ປະເພດຂໍ້ຜິດພາດ
 * @var string $message ຂໍ້ຄວາມຂໍ້ຜິດພາດ
 * @var array $details ລາຍລະອຽດຂໍ້ຜິດພາດ
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
            --secondary-color: #2c3e50;
            --background-color: #f5f5f5;
            --text-color: #333;
            --border-color: #ddd;
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
            padding: 2rem;
        }

        .error-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .error-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .error-type {
            font-size: 1rem;
            opacity: 0.9;
        }

        .error-content {
            padding: 2rem;
        }

        .error-message {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #fff5f5;
            border-left: 4px solid var(--primary-color);
            border-radius: 0 4px 4px 0;
        }

        .error-details {
            background: #f8f9fa;
            border-radius: 4px;
            overflow: hidden;
        }

        .details-section {
            margin-bottom: 1.5rem;
        }

        .details-title {
            background: var(--secondary-color);
            color: white;
            padding: 0.5rem 1rem;
            font-weight: bold;
        }

        .details-content {
            padding: 1rem;
            overflow-x: auto;
        }

        .stack-trace {
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 0.9rem;
            background: #2d3436;
            color: #dfe6e9;
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
        }

        .request-info {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 0.5rem;
        }

        .request-info dt {
            font-weight: bold;
            color: var(--secondary-color);
        }

        .error-footer {
            padding: 1rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }

        .home-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .home-button:hover {
            background: #c0392b;
        }

        code {
            font-family: monospace;
            background: #f1f2f3;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <header class="error-header">
            <div>
                <div class="error-title">Debug Error</div>
                <div class="error-type">Type: <?= htmlspecialchars($type) ?></div>
            </div>
            <div>Environment: DEVELOPMENT</div>
        </header>

        <div class="error-content">
            <div class="error-message">
                <?= htmlspecialchars($message) ?>
                <?=$details['driver_message']??"" ?>
            </div>

            <?php if (!empty($details)): ?>
                <div class="error-details">
                    <?php if (isset($details['file'])): ?>
                        <div class="details-section">
                            <div class="details-title">Error Location</div>
                            <div class="details-content">
                                <div>File: <code><?= htmlspecialchars($details['file']) ?></code></div>
                                <div>Line: <code><?= htmlspecialchars($details['line']) ?></code></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($details['trace'])): ?>
                        <div class="details-section">
                            <div class="details-title">Stack Trace</div>
                            <div class="details-content">
                                <div class="stack-trace"><?= htmlspecialchars($details['trace']) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="details-section">
                        <div class="details-title">Request Information</div>
                        <div class="details-content">
                            <dl class="request-info">
                                <dt>URL:</dt>
                                <dd><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></dd>
                                <dt>HTTP Method:</dt>
                                <dd><?= htmlspecialchars($_SERVER['REQUEST_METHOD']) ?></dd>
                                <dt>IP Address:</dt>
                                <dd><?= htmlspecialchars($_SERVER['REMOTE_ADDR']) ?></dd>
                                <dt>Time:</dt>
                                <dd><?= date('Y-m-d H:i:s') ?></dd>
                                <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
                                <dt>Referer:</dt>
                                <dd><?= htmlspecialchars($_SERVER['HTTP_REFERER']) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="error-footer">
            <a href="<?= BASE_URL ?>" class="home-button">ກັບຄືນໜ້າຫຼັກ</a>
        </div>
    </div>
</body>
</html>