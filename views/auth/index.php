<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex flex-column align-items-center mb-5 gap-2">
                        <img src="<?= Helper::asset('images/ltvclogo.png') ?>" alt="logo" width="100">
                        <h3>ລະບົບຫ້ອງສະໝຸດ</h3>
                        <h4>ວິທະຍາໄລເຕັກນິກວິຊາຊີບ-ຫຼວງພະບາງ</h4>
                    </div>
                    <form action="<?= Helper::url('auth/login') ?>" method="POST">
                        <?= $this->useCsrf(); ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">ຊື່ຜູ້ໃຊ້</label>
                            <input type="text" class="form-control" name="username" value="<?= $this->old('username'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">ລະຫັດຜ່ານ</label>
                            <input type="password" class="form-control" name="password" value="<?= $this->old('password'); ?>" required>
                        </div>
                        <?= Helper::flash() ?>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">ຈົດຈຳການເຂົ້າສູ່ລະບົບ</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">ເຂົ້າສູ່ລະບົບ</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">ລືມລະຫັດຜ່ານ?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>