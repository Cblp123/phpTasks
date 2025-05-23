<h1>Авторизация</h1>

<div class="row">
    <div class="col-md-6">
        <?php if (isset($errors) && !empty($errors['email'])): ?>
            <div class="alert alert-danger">
                <?php echo $errors['email'][0] ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Почта</label>
                <input type="email" name="email" class="form-control" value="<?php echo $model->email ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
        </form>
    </div>
</div> 