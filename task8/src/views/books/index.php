<?php
use App\Core\Application;

?>
<h1>Библиотека</h1>

<?php if (!$isGuest): ?>
    <p>
        <a href="/books/create" class="btn btn-success">Добавить книгу</a>
    </p>
<?php endif; ?>

<div class="row">
    <?php foreach ($books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if ($book->cover_image): ?>
                    <div class="card-img-container" style="height: 300px; overflow: hidden;">
                        <img src="/uploads/<?php echo $book->cover_image ?>" 
                             class="card-img-top" 
                             alt="<?php echo $book->title ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                <?php else: ?>
                    <div class="card-img-container bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <span class="text-muted">Нет обложки</span>
                    </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo $book->title ?></h5>
                    <p class="card-text">Автор: <?php echo $book->author ?></p>
                    <p class="card-text">Дата прочтения: <?php echo $book->read_date ?></p>
                    
                    <div class="mt-auto">
                        <div class="d-flex gap-2">
                            <?php if ($book->book_file && ($book->canDownload() || $book->allow_download && $isGuest)): ?>
                                <a href="/uploads/<?php echo $book->book_file ?>" class="btn btn-primary" download>Скачать</a>
                            <?php endif; ?>

                            <?php if (!$isGuest && $book->user_id === Application::$app->session->get('user')): ?>
                                <a href="/books/edit?id=<?php echo $book->id ?>" class="btn btn-warning">Редактировать</a>
                                <form action="/books/delete" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $book->id ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> 