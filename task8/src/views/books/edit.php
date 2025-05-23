<?php
/** @var $model \App\models\Book */
?>

<h1>Редактирование книги: <?php echo $model->title ?></h1>

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $model->id ?>">
    <div class="form-group">
        <label>Название</label>
        <input type="text" name="title" class="form-control" value="<?php echo $model->title ?>">
    </div>
    <div class="form-group">
        <label>Автор</label>
        <input type="text" name="author" class="form-control" value="<?php echo $model->author ?>">
    </div>
    <div class="form-group">
        <label>Обложка</label>
        <?php if ($model->cover_image): ?>
            <div class="mb-2">
                <img src="/uploads/<?php echo $model->cover_image ?>" alt="Текущая обложка" style="max-width: 200px;">
                <p class="text-muted">Текущая обложка</p>
                <div class="form-check mb-2">
                    <input type="checkbox" name="remove_cover" id="remove_cover" class="form-check-input">
                    <label class="form-check-label" for="remove_cover">Удалить обложку</label>
                </div>
            </div>
        <?php endif; ?>
        <input type="file" name="cover_image" class="form-control">
        <small class="text-muted">Оставьте пустым, чтобы сохранить текущую обложку</small>
    </div>
    <div class="form-group">
        <label>Файл книги</label>
        <?php if ($model->book_file): ?>
            <div class="mb-2">
                <p class="text-muted">Текущий файл: <?php echo $model->book_file ?></p>
            </div>
        <?php endif; ?>
        <input type="file" name="book_file" class="form-control">
        <small class="text-muted">Оставьте пустым, чтобы сохранить текущий файл</small>
    </div>
    <div class="form-group">
        <label>Дата прочтения</label>
        <input type="date" name="read_date" class="form-control" value="<?php echo $model->read_date ?>">
    </div>
    <div class="form-group">
        <label>
            <input type="checkbox" name="allow_download" value="1" <?php echo $model->allow_download ? 'checked' : '' ?>>
            Разрешить скачивание
        </label>
    </div>
    <button type="submit" class="btn btn-primary">Обновить</button>
    <a href="/books" class="btn btn-secondary">Отмена</a>
</form> 