<div class="content">
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3>Dashboard</h3>
        </div>

        <div class="col-auto ms-auto text-end mt-n1">
        </div>
    </div>
    <?= Helper::flash(true) ?>
    <div class="d-flex justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center border rounded col ps-3 pe-3 py-3 bg-dark-subtle">
            <div>
                <h5 class="text-body-secondary">title</h5>
                <h2>2000</h2>
            </div>
            <div class="ms-auto bg-light rounded-circle ps-4 pe-4 py-3">
                <i class="bi bi-1-square-fill fs-5"></i>
            </div>
        </div>
        <div class="d-flex align-items-center border rounded col ps-3 pe-3 py-3 bg-dark-subtle">
            <div>
                <h5 class="text-body-secondary">title</h5>
                <h2>2000</h2>
            </div>
            <div class="ms-auto bg-light rounded-circle ps-4 pe-4 py-3">
                <i class="bi bi-1-square-fill fs-5"></i>
            </div>
        </div>
        <div class="d-flex align-items-center border rounded col ps-3 pe-3 py-3 bg-dark-subtle">
            <div>
                <h5 class="text-body-secondary">title</h5>
                <h2>2000</h2>
            </div>
            <div class="ms-auto bg-light rounded-circle ps-4 pe-4 py-3">
                <i class="bi bi-1-square-fill fs-5"></i>
            </div>
        </div>
        <div class="d-flex align-items-center border rounded col ps-3 pe-3 py-3 bg-dark-subtle">
            <div>
                <h5 class="text-body-secondary">title</h5>
                <h2>2000</h2>
            </div>
            <div class="ms-auto bg-light rounded-circle ps-4 pe-4 py-3">
                <i class="bi bi-1-square-fill fs-5"></i>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <?php
        $table = new JoTable("ລຳດັບ", "ປື້ມ", "ສະມາຊິກ", "ວັນທີ່ຢືມ", "ວັນກຳນົດສົ່ງ");
        $table->setClassHeader("table-header-main text-center");
        $table->enableSearch([1, 2, 3, 4]);

        $table->addRow([
            $table->cell($table->autoNum(), "class='text-center'"),
            $table->cell("ປື້ມ01", "class='text-center'"),
            $table->cell("ສະມາຊິກ1", "class='text-center'"),
            $table->cell("20/02/2024", "class='text-center'"),
            $table->cell("20/02/2024", "class='text-center'"),
        ]);
        $table->addRow([
            $table->cell($table->autoNum(), "class='text-center'"),
            $table->cell("ປື້ມ02", "class='text-center'"),
            $table->cell("ສະມາຊິກ2", "class='text-center'"),
            $table->cell("20/02/2024", "class='text-center'"),
            $table->cell("20/02/2024", "class='text-center'"),
        ]);
        $table->addRow([
            $table->cell($table->autoNum(), "class='text-center'"),
            $table->cell("ປື້ມ03", "class='text-center'"),
            $table->cell("ສະມາຊິກ3", "class='text-center'"),
            $table->cell("20/02/2024", "class='text-center'"),
            $table->cell("20/02/2024", "class='text-center'"),
        ]);
        $table->render();
        ?>
    </div>
</div>