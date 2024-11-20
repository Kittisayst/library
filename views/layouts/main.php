<!DOCTYPE html>
<html lang="lo" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? "ລະບົບຫ້ອງສະໝຸດ") ?></title>
    <link rel="shortcut icon" href="<?= Helper::asset('images/ltvclogo.png') ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Meta Tags -->
    <?php if (!empty($metaTags)): ?>
        <?php foreach ($metaTags as $name => $content): ?>
            <meta name="<?= htmlspecialchars($name) ?>" content="<?= htmlspecialchars($content) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- CSS -->
    <?php if (!empty($css)): ?>
        <?php foreach ($css as $file): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($file) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <header>
        <?= $this->renderPartial('partials/header') ?>
    </header>

    <main class="container">
        <?= $content ?>
    </main>

    <footer class="bg-dark text-white pt-5 pb-4">
        <?= $this->renderPartial('partials/footer') ?>
    </footer>

    <!-- JavaScript -->
    <?php if (!empty($js)): ?>
        <?php foreach ($js as $file): ?>
            <script src="<?= htmlspecialchars($file) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>