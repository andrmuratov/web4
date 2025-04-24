<?php

header('Content-Type: text/html; charset=UTF-8');

$user = 'u68675';
$pass = '5180478';
$db = new PDO('mysql:host=localhost;dbname=u68675', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
function getLangs($db){
  try{
    $allowed_lang=[];
    $data = $db->query("SELECT lang_name FROM prog_lang")->fetchAll();
    foreach ($data as $lang) {
      $lang_name = $lang['lang_name'];
      $allowed_lang[$lang_name] = $lang_name;
    }
    return $allowed_lang;
  } catch(PDOException $e){
    print('Error: ' . $e->getMessage());
    exit();
  }
}
$allowed_lang=getLangs($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $messages = array();

  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    $messages[] = 'Спасибо, результаты сохранены.';
  }

  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['number'] = !empty($_COOKIE['number_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['gen'] = !empty($_COOKIE['gen_error']);
  $errors['lang'] = !empty($_COOKIE['lang_error']);
  $errors['bdate'] = !empty($_COOKIE['bdate_error']);
  $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

  if ($errors['fio']) {
    if($_COOKIE['fio_error']=='1'){
      $messages[] = '<div class="error">Имя не указано.</div>';
    }
    elseif($_COOKIE['fio_error']=='2'){
      $messages[] = '<div class="error">Введенное имя указано некорректно. Имя не должно превышать 128 символов.</div>';
    }
    else{
      $messages[] = '<div class="error">Введенное имя указано некорректно. Имя должно содержать только буквы и пробелы.</div>';
    }
    setcookie('fio_error', '', 100000);
    setcookie('fio_value', '', 100000);
  }

  if ($errors['number']) {
    if($_COOKIE['number_error']=='1'){
      $messages[] = '<div class="error">Номер не указан.</div>';
    }
    elseif($_COOKIE['number_error']=='2'){
      $messages[] = '<div class="error">Номер указан некорректно.</div>';
    }
    setcookie('number_error', '', 100000);
    setcookie('number_value', '', 100000);
  }

  if ($errors['email']) {
    if($_COOKIE['email_error']=='1') {
      $messages[] = '<div class="error">Email не указан.</div>';
    }
    elseif($_COOKIE['email_error']=='2') {
      $messages[] = '<div class="error">Введенный email указан некорректно.</div>';
    }
    setcookie('email_error', '', 100000);
    setcookie('email_value', '', 100000);
  }

  if ($errors['gen']) {
    if($_COOKIE['gen_error']=='1'){
      $messages[] = '<div class="error">Пол не указан.</div>';
    }
    elseif($_COOKIE['gen_error']=='2'){
      $messages[] = '<div class="error">Поле "пол" содержит недопустимое значение.</div>';
    }
    setcookie('gen_error', '', 100000);
    setcookie('gen_value', '', 100000);
  }

  if ($errors['bio']) {
    if($_COOKIE['bio_error']=='1'){
      $messages[] = '<div class="error">Заполните биографию.</div>';
    }
    elseif($_COOKIE['bio_error']=='2'){
      $messages[] = '<div class="error">Количество символов в поле "биография" не должно превышать 512.</div>';
    }
    elseif($_COOKIE['bio_error']=='3'){
      $messages[] = '<div class="error">Поле "биография" содержит недопустимые символы.</div>';
    }
    setcookie('bio_error', '', 100000);
    setcookie('bio_value', '', 100000);
  }

  if ($errors['lang']) {
    if($_COOKIE['lang_error']=='1'){
      $messages[] = '<div class="error">Укажите любимый(ые) язык(и) программирования.</div>';
    }
    elseif($_COOKIE['lang_error']=='2'){
      $messages[] = '<div class="error">Указан недопустимый язык.</div>';
    }
    setcookie('lang_error', '', 100000);
    setcookie('lang_value', '', 100000);
  }

  if ($errors['bdate']) {
    setcookie('bdate_error', '', 100000);
    setcookie('bdate_value', '', 100000);
    $messages[] = '<div class="error">Дата рождения не указана.</div>';
  }

  if ($errors['checkbox']) {
    setcookie('checkbox_error', '', 100000);
    setcookie('checkbox_value', '', 100000);
    $messages[] = '<div class="error">Подтвердите, что вы ознакомлены с контрактом.</div>';
  }



  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['number'] = empty($_COOKIE['number_value']) ? '' : $_COOKIE['number_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  $values['gen'] = empty($_COOKIE['gen_value']) ? '' : $_COOKIE['gen_value'];
  $values['lang'] = empty($_COOKIE['lang_value']) ? '' : $_COOKIE['lang_value'];
  $values['bdate'] = empty($_COOKIE['bdate_value']) ? '' : $_COOKIE['bdate_value'];
  $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : $_COOKIE['checkbox_value'];

  include('form.php');

  //exit();
}
else {

  $fio = $_POST['fio'];
  $num = $_POST['number'];
  $email = $_POST['email'];
  $bdate = $_POST['birthdate'];
  $biography = $_POST['biography'];
  $gen = $_POST['radio-group-1'];
  //$checkbox= $_POST['checkbox'];
  //$allowed_lang = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Clojure", "Haskel", "Prolog", "Scala", "Go"];
  $languages = $_POST['languages'] ?? []; 

  $errors = FALSE;


  if (empty($fio)) {
    //print('Имя не указано.<br/>');
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } elseif (strlen($fio) > 128 ) {
    //print('Введенное имя указано некорректно. Имя не должно превышать 128 символов.<br/>');
    setcookie('fio_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  } elseif ( !preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]+$/u', $fio)) {
    //print('Введенное имя указано некорректно. Имя должно содержать только буквы и пробелы.<br/>');
    setcookie('fio_error', '3', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('fio_value', $fio, time() + 365 * 24 * 60 * 60);


  if (empty($num)) {
    //print('Номер не указан.<br/>');
    setcookie('number_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } elseif (!preg_match('/^\+7\d{10}$/', $num)) {
    //print('Номер указан некорректно.<br/>');
    setcookie('number_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('number_value', $num, time() + 365 * 24 * 60 * 60);

  if (empty($email) ) {
    //print('Email не указан.<br/>');
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    //print('Введенный email указан некорректно.<br/>');
    setcookie('email_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('email_value', $email, time() + 365 * 24 * 60 * 60);

  if (empty($gen)){
    //print ('Пол не указан.<br/>');
    setcookie('gen_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else{
    $allowed_genders = ["male", "female"];
    if (!in_array($gen, $allowed_genders)) {
      setcookie('gen_error', '2', time() + 24 * 60 * 60);
      //print('Поле "пол" содержит недопустимое значение.<br/>');
      $errors = TRUE;
    }
  }
  setcookie('gen_value', $gen, time() + 365 * 24 * 60 * 60);

  if (empty($biography)) {
    //print('Заполните биографию.<br/>');
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } elseif(strlen($biography) > 512){
    //print('Количество символов в поле "биография" не должно превышать 512.<br/>');
    setcookie('bio_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  } elseif(preg_match('/[<>{}\[\]]|<script|<\?php/i', $biography)){
    //print('Поле "биография" содержит недопустимые символы.<br/>');
    setcookie('bio_error', '3', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('bio_value', $biography, time() + 365 * 24 * 60 * 60);

  if(empty($languages)) {
    //print('Укажите любимый(ые) язык(и) программирования.<br/>');
    setcookie('lang_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    foreach ($languages as $lang) {
      if (!in_array($lang, $allowed_lang)) {
          //print('Указан недопустимый язык ($lang).<br/>');
          setcookie('lang_error', '2', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
    }
  }
  $langs_value =(implode(",", $languages));
  setcookie('lang_value', $langs_value, time() + 365 * 24 * 60 * 60);

  if(empty($bdate)) {
    //print('Дата рождения не указана.<br/>');
    setcookie('bdate_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('bdate_value', $bdate, time() + 365 * 24 * 60 * 60);

  if (!isset($_POST["checkbox"])) {
    //print('Подтвердите, что вы ознакомлены с контрактом.<br/>');
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('checkbox_value', $_POST["checkbox"], time() + 365 * 24 * 60 * 60);


  if ($errors) {
    header('Location: index.php');
    exit();
  }
  else {
    setcookie('fio_error', '', 100000);
    setcookie('number_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('gen_error', '', 100000);
    setcookie('lang_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
    setcookie('bdate_error', '', 100000);
  }
  
/////
  //$user = 'u68607';
  //$pass = '7232008';
  //$db = new PDO('mysql:host=localhost;dbname=u68607', $user, $pass,
    //[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $table_app = 'application';
  $table_lang = 'prog_lang';
  $table_ul='user_lang';

  try{
    //$data = array( 'fio' => $fio, 'num' => $num, 'email' => $email, 'bdate' => $bdate, 'gen' => $gen, 'biography' => $biography, 'checkbox' => $_POST["checkbox"]); 
    $stmt = $db->prepare("INSERT INTO application (fio, number, email, bdate, gender, biography, checkbox ) values (?, ?, ?, ?, ?, ?, ? )");
    $stmt->execute([$_POST['fio'], $_POST['number'], $_POST['email'], $_POST['birthdate'], $_POST['radio-group-1'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0]);
  } 
  catch (PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }

  $id=$db->lastInsertId();
  try{
  
    $stmt_select = $db->prepare("SELECT id_lang FROM prog_lang WHERE lang_name = ?");
    $stmt_insert = $db->prepare("INSERT INTO user_lang (id, id_lang) VALUES (?, ?)");
    foreach ($languages as $language) {
      $stmt_select ->execute([$language]);
      $id_lang = $stmt_select->fetchColumn();
      
      if ($id_lang) {
        $stmt_insert->execute([$id, $id_lang]);
      }
    }
  } 
  catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
  }

////
  setcookie('save', '1');
////

  header('Location: index.php');
}