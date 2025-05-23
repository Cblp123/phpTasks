<?php
/** @var $model \App\models\Book */
?>

<h1>Добавление новой книги</h1>

<div class="row">
    <div class="col-md-6">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text" name="title" class="form-control <?php echo $model->hasError('title') ? 'is-invalid' : '' ?>" 
                       value="<?php echo $model->title ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('title') ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Автор</label>
                <input type="text" name="author" class="form-control <?php echo $model->hasError('author') ? 'is-invalid' : '' ?>"
                       value="<?php echo $model->author ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('author') ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Обложка</label>
                <input type="file" name="cover_image" class="form-control" accept="image/png,image/jpeg">
            </div>
            <div class="mb-3">
                <label class="form-label">Файл книги</label>
                <input type="file" name="book_file" class="form-control <?php echo $model->hasError('book_file') ? 'is-invalid' : '' ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('book_file') ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата прочтения</label>
                <input type="date" name="read_date" class="form-control <?php echo $model->hasError('read_date') ? 'is-invalid' : '' ?>"
                       value="<?php echo $model->read_date ?>">
                <div class="invalid-feedback">
                    <?php echo $model->getFirstError('read_date') ?>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="allow_download" class="form-check-input" value="1" <?php echo $model->allow_download ? 'checked' : '' ?>>
                <label class="form-check-label">Разрешить скачивание</label>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/books" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div> 