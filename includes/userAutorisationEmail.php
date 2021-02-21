<?php
if (! is_null ( $user_pass )) {
	$passwordMsg = '<p>' . sprintf ( __ ( 'Username: %s' ), $user_login ) . '<br />' . __ ( 'Password' ) . ': ' . $user_pass . '</p>
                    <p>Чтобы установить новый пароль, перейдите по ссылке: ' . wp_login_url ( home_url () . '/login/' ) . '&action=lostpassword</p>';
} else {
	$passwordMsg = '';
}
$message = '<div style="font: 14px/20px Arial; padding: 15px 20px;max-width: 960px; margin: 0 auto; background-color: #EFEFEF;">
  <div style="text-align: center">
    <a href="kabacademy.com">
      <img
        src="http://kabacademy.com/wp-content/uploads/2015/04/logo_top.png">
    </a>
  </div>
  <div style="margin: 20px 0; padding: 10px; border: 2px solid #00adef; border-radius: 5px;">
    <p>' . $display_name . ', здравствуйте!</p>
    <p>Вы зарегистрированы на сайте Международной академии каббалы,
      крупнейшем в мире учебно-образовательном, бесплатном и
      неограниченном источнике достоверной информации о науке каббала.</p>
    ' . $passwordMsg . '
  </div>
  <div>

    <p>Позвольте рассказать вам о том, как пользоваться сайтом
      академии, чтобы вы могли использовать все возможности предлагаемого
      обучения.</p>
    <p>На сайте представлены различные курсы на выбор:</p>
  </div>
  <div class="clearfix">
    <img
      src="' . MAILCHIMPINT_DIR_URL . '/includes/images/webinar.png"
      style="   float: left;margin: 0 10px 10px 0;border: 0 none;outline: none;width: 160px;" alt=""/>
    <p>
      <span style="color: #00adef;font: bold 16px Arial;">Вебинар Михаэля Лайтмана</span>
    </p>
    <p>
      <strong>Об этом курсе:</strong>
    </p>
    <p>Михаэль Лайтман, чьи выступления вы ранее могли смотреть на
      телевидении и в сети интернет, проводит еженедельные встречи в
      формате вебинаров, где отвечает на вопросы зрителей в прямом эфире.</p>
    <p>Это курс для тех, кто желает получить ответы на вопросы о
      смысле жизни быстро, непосредственно от каббалиста.</p>
  </div>
  <div style="text-align: center">
    <a href="http://kabacademy.com/course/webinar-michael-laitman/"
       style="display: inline-block;padding: 10px 15px;font: 16px/30px Arial;border-radius: 2px;background-color: #e0922f;text-decoration: none;text-transform: uppercase;cursor: pointer;white-space: nowrap;color: #ffffff;">
      Записаться на курс </a>
  </div>
  <h2>Курсы для самостоятельного обучения</h2>
  <div class="clearfix">
    <div style="display: inline-block;width: 49%;vertical-align: top;">
      <img
        src="' . MAILCHIMPINT_DIR_URL . '/includes/images/vvodnyi.png"
        style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt=""/>
      <p>
        <a href="http://kabacademy.com/course/vvodnyj/">
          <strong>
            Вводный курс </strong>
        </a>
        <br/> Все, что человек сумел узнать и понять о себе и о мире …
        <br/>
        <a href="http://kabacademy.com/course/vvodnyj/">Программа курса
          >></a>
      </p>
    </div>
    <div style="display: inline-block;width: 49%;vertical-align: top;">
      <img
        src="' . MAILCHIMPINT_DIR_URL . '/includes/images/teoriya.png"
        style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt=""/>
      <p>
        <a
          href="http://kabacademy.com/course/4-kabbalisticheskaya-teoriya-razvitiya-mira/">
          <strong> Теория развития мира </strong>
        </a>
        <br/> Процесс образования материи нашего мира, причины появления
        первого живого организма.
        <br/>
        <a
          href="http://kabacademy.com/course/4-kabbalisticheskaya-teoriya-razvitiya-mira/">Программа
          курса >></a>
      </p>
    </div>
    <div style="display: inline-block;width: 49%;vertical-align: top;">
      <img
        src="' . MAILCHIMPINT_DIR_URL . '/includes/images/kab_and_religion.png"
        style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt=""/>
      <p>
        <a href="http://kabacademy.com/course/8-kabbala-i-religiya/">
          <strong>
            Каббала и религия </strong>
        </a>
        <br/> Сравнительный анализ науки каббала и религии.
        <br/>
        <a
          href="http://kabacademy.com/course/8-kabbala-i-religiya/">Программа
          курса >></a>
      </p>
    </div>
    <div style="display: inline-block;width: 49%;vertical-align: top;">
      <img
        src="' . MAILCHIMPINT_DIR_URL . '/includes/images/vospriyatie.png"
        style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt=""/>
      <p>
        <a href="http://kabacademy.com/course/3-vospriyatie-realnosti/">
          <strong> Восприятие реальности </strong>
        </a>
        <br/> Исследования скрытой части реальности - вне времени,
        движения и пространства.
        <br/>
        <a
          href="http://kabacademy.com/course/3-vospriyatie-realnosti/">Программа
          курса >></a>
      </p>
    </div>

    <div>
      <em>* Обратите внимание. Некоторые курсы доступны только после
        прохождения вводного курса.</em>
    </div>
  </div>
  <div style="font-style: italic; margin: 50px 0 20px;">
    <ul style="list-style-position: inside;padding-left: 10px;">
      <li>курсы для самостоятельного обучения (*) дают возможность
        самим планировать свой график прохождения уроков и выбирать
        наиболее интересующие вас темы
      </li>
      <li>вебинары с профессором Михаэлем Лайтманом для тех, кто
        только знакомится с наукой каббала и хочет получить ответы на свои
        вопросы от каббалиста
      </li>
    </ul>
  </div>
  <div>
    <p>
      <strong>Также предлагаем вам</strong>
    </p>
    <ul style="list-style-position: inside;padding-left: 10px;">
      <li>
        <a
          href="http://kabacademy.com/programm-online/uznat-bolshe-ob-obuchenii-v-mak/programma-obucheniya/">
          ознакомиться с подробной программой обучения </a>
      </li>
      <li>
        <a
          href="http://kabacademy.com/programm-online/uznat-bolshe-ob-obuchenii-v-mak/uchebnyie-materialyi/">
          скачать учебные материалы </a>
      </li>
      <li>
        <a
          href="http://kabacademy.com/programm-online/uznat-bolshe-ob-obuchenii-v-mak/nashi-prepodavateli/">
          познакомиться с преподавателями академии </a>
      </li>
      <li>
        <a
          href="http://kabacademy.com/programm-online/uznat-bolshe-ob-obuchenii-v-mak/services/">
          воспользоваться дополнительными сервисами для студентов </a>
      </li>
    </ul>
    <p>Желаем вам успехов в обучении и увлекательного путешествия в
      глубины своей души!</p>
  </div>
</div>';
?>
