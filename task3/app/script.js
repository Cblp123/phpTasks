document.getElementById('feedbackForm').addEventListener('submit', function (event) {
    event.preventDefault();
    // флаг валидности введеных данных
    let valid = true;

    // получаем поля формы для проверки
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const phone = document.getElementById('phone');
    const comment = document.getElementById('comment');

    // Регулярки для проверки
    const namePattern = /^[А-Яа-яЁё\- ]+$/; // Только русские буквы, пробелы и тире
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // example@mail.com
    const phonePattern = /^89\d{9}$/; // 89XXXXXXXXX 
    
    // Очистка ошибок перед повторной проверкой
    [name, email, phone, comment].forEach(field => field.classList.remove('error'));

    // Проверка имени
    if (!namePattern.test(name.value.trim())) {
        showError(name);
        valid = false;
    }
    // Проверка email
    if (!emailPattern.test(email.value.trim())) {
        showError(email);
        valid = false;
    }
    // Проверка телефона
    if (!phonePattern.test(phone.value.trim())) {
        showError(phone);
        valid = false;
    }

    // комментарий должен содержать хотя бы 1 символ
    if (comment.value.trim().length < 1) {
        showError(comment);
        valid = false;
    }

    // Если валидация успешна — отправляем данные
    if (valid) {
        // Создаем объект FormData для отправки данных
        let formData = new FormData(this);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'form.php', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('feedbackForm').style.display = 'none'; // Скрываем форму
                document.getElementById('message').innerHTML = xhr.responseText; // Показываем сообщение
            } else {
                alert('Ошибка отправки. Попробуйте снова.');
            }
        };

        xhr.send(formData);
    }
});

// Функция добавления анимации ошибки
function showError(field) {
    field.classList.add('error');
}
