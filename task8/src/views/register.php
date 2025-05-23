<h1>Регистрация</h1>

<div class="row">
    <div class="col-md-6">
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Почта</label>
                <input type="email" name="email" class="form-control <?php echo $model->hasError('email') ? 'is-invalid' : '' ?>" 
                       value="<?php echo $model->email ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('email') ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input type="password" name="password" class="form-control <?php echo $model->hasError('password') ? 'is-invalid' : '' ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('password') ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Подтвердите пароль</label>
                <input type="password" name="confirmPassword" class="form-control <?php echo $model->hasError('confirmPassword') ? 'is-invalid' : '' ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('confirmPassword') ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </form>
    </div>
</div> 