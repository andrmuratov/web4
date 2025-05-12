<?php
// Получаем данные из cookies
$errors = []; // Массив для хранения ошибок
$oldValues = []; // Массив для хранения предыдущих значений полей
$savedValues = []; // Массив для хранения сохраненных значений

// Проверяем наличие ошибок в cookies
if (isset($_COOKIE['form_errors'])) {
    $errors = json_decode($_COOKIE['form_errors'], true); // Декодируем ошибки из JSON
    $oldValues = json_decode($_COOKIE['old_values'], true); // Декодируем старые значения из JSON
}

// Получаем сохраненные значения из cookies
foreach ($_COOKIE as $name => $value) {
    if (strpos($name, 'saved_') === 0) { // Ищем cookies с префиксом 'saved_'
        $field = substr($name, 6); // Извлекаем имя поля (удаляем 'saved_')
        $savedValues[$field] = $value; // Сохраняем значение в массив
    }
}

/**
 * Функция для получения значения поля формы
 * @param string $field Имя поля
 * @param string $default Значение по умолчанию
 * @return string Значение поля или значение по умолчанию
 */
function getFieldValue($field, $default = '') {
    global $oldValues, $savedValues;

    // Сначала проверяем старые значения (из последней отправки формы)
    if (isset($oldValues[$field])) {
        return $oldValues[$field];
    }

    // Затем проверяем сохраненные значения (из cookies)
    if (isset($savedValues[$field])) {
        return $savedValues[$field];
    }

    // Если ничего не найдено, возвращаем значение по умолчанию
    return $default;
}

/**
 * Функция для проверки выбранного значения в select или radio
 * @param string $field Имя поля
 * @param string $value Значение для проверки
 * @return string 'selected' или 'checked', если значение совпадает, иначе пустая строка
 */
function isSelected($field, $value) {
    global $oldValues, $savedValues;

    $currentValues = [];
    if (isset($oldValues[$field])) {
        if ($field === 'languages') {
            // Для множественного выбора languages разбиваем строку на массив
            $currentValues = explode(',', $oldValues[$field]);
        } else {
            // Для одиночных значений сравниваем напрямую
            return $oldValues[$field] === $value ? 'checked' : '';
        }
    } elseif (isset($savedValues[$field])) {
        if ($field === 'languages') {
            $currentValues = explode(',', $savedValues[$field]);
        } else {
            return $savedValues[$field] === $value ? 'checked' : '';
        }
    }

    // Для множественного выбора проверяем наличие значения в массиве
    return in_array($value, $currentValues) ? 'selected' : '';
}

/**
 * Функция для проверки состояния чекбокса
 * @param string $field Имя поля
 * @return string 'checked', если чекбокс был отмечен, иначе пустая строка
 */
function isChecked($field) {
    global $oldValues, $savedValues;

    // Проверяем сначала старые значения
    if (isset($oldValues[$field])) {
        return $oldValues[$field] ? 'checked' : '';
    }

    // Затем проверяем сохраненные значения
    if (isset($savedValues[$field])) {
        return $savedValues[$field] ? 'checked' : '';
    }

    return '';
}
?>

<!DOCTYPE html>
<html lang="ru-RU">

  <head>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <!-- Подключение Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
  </head>

  <body>
    <!-- Сообщение об успешном сохранении -->
    <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Данные успешно сохранены!</div>
            <?php endif; ?>

            <!-- Вывод списка ошибок, если они есть -->
            <?php if (!empty($errors)): ?>
                <div class="error-list">
                    <h3>Обнаружены ошибки:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
    
    <!-- Основная форма -->
    <form action="a.php" method="POST" id="form" class="w-50 mx-auto">

          <!-- Поле для ввода ФИО -->
          <label class="form-label">
            1) ФИО:<br>
            <input type="text" class="form-control" placeholder="Введите ваше ФИО" name="name" id = "name" required
                           value="<?php echo htmlspecialchars(getFieldValue('name')); ?>"
                           class="<?php echo isset($errors['name']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
          </label><br>

          <!-- Поле для ввода телефона -->
          <label class="form-label">
            2) Телефон:<br>
            <input class="form-control" type="tel" placeholder="+79200000000" name="phone" id="phone" required
                           value="<?php echo htmlspecialchars(getFieldValue('phone')); ?>"
                           class="<?php echo isset($errors['phone']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['phone']); ?></div>
                    <?php endif; ?>
          </label><br>

          <!-- Поле для ввода email -->
          <label class="form-label">
            3) e-mail:<br>
            <input class="form-control" type="email" placeholder="Введите вашу почту" name="email" id="email" required
                           value="<?php echo htmlspecialchars(getFieldValue('email')); ?>"
                           class="<?php echo isset($errors['email']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
          </label><br>

          <!-- Поле для выбора даты рождения -->
          <label class="form-label">
            4) Дата рождения:<br>
            <input class="form-control" value="2006-01-19" type="date" name="birthdate" id="birthdate" required
                           value="<?php echo htmlspecialchars(getFieldValue('birthdate')); ?>"
                           class="<?php echo isset($errors['birthdate']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['birthdate'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['birthdate']); ?></div>
                    <?php endif; ?>
          </label><br>
          
          <!-- Группа радиокнопок для выбора пола -->
          <div><br>
            5) Пол:<br>
          <label class="form-check-label"><input type="radio" checked="checked" class="form-check-input" value="male" id="male" name="gender" required
                               <?php echo isSelected('gender', 'male'); ?>
                               class="<?php echo isset($errors['gender']) ? 'error-field' : ''; ?>">>
            Мужской</label>
          <label class="form-check-label"><input type="radio" class="form-check-input" value="female" id="female" name="gender"
                               <?php echo isSelected('gender', 'female'); ?>
                               class="<?php echo isset($errors['gender']) ? 'error-field' : ''; ?>">>
            Женский</label><br>
            <?php if (isset($errors['gender'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['gender']); ?></div>
                    <?php endif; ?>
          </div><br>

          <!-- Множественный выбор языков программирования -->
          <label class="form-label">
            6) Любимый язык программирования:<br>
            <select class="form-select" id="languages" name="languages[]" multiple="multiple" required class="<?php echo isset($errors['languages']) ? 'error-field' : ''; ?>" size="5">
                        <?php
                        // Список всех доступных языков программирования
                        $allLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
                        foreach ($allLanguages as $lang): ?>
                            <option value="<?php echo htmlspecialchars($lang); ?>"
                                <?php echo isSelected('languages', $lang); ?>>
                                <?php echo htmlspecialchars($lang); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['languages'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['languages']); ?></div>
                    <?php endif; ?>
          </label><br>

          <!-- Поле для ввода биографии -->
          <label class="form-label">
            7) Биография:<br>
            <input type="text" class="form-control" id="bio" name="bio" required
                              class="<?php echo isset($errors['bio']) ? 'error-field' : ''; ?>"><?php
                              echo htmlspecialchars(getFieldValue('bio')); ?></textarea>
                    <?php if (isset($errors['bio'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['bio']); ?></div>
                    <?php endif; ?>
          </label><br>

          <!-- Чекбокс согласия -->
            8):<br>
          <label class="form-check-label"><input type="checkbox" class="form-check-input" name="agreement" id="agreement" value="1" required
                           <?php echo isChecked('agreement'); ?>
                           class="<?php echo isset($errors['agreement']) ? 'error-field' : ''; ?>">
                    С контрактом ознакомлен(а)
                    <?php if (isset($errors['agreement'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['agreement']); ?></div>
                    <?php endif; ?>
          </label><br>

          <!-- Кнопка отправки формы -->
            9)Кнопка:<br>
          <button type="submit" name="save" class="btn btn-primary">Опубликовать</button>
    </form>
  </body>
</html>