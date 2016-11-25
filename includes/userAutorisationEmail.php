<?php
if (! is_null ( $user_pass )) {
	$passwordMsg = '<p>' . sprintf ( __ ( 'Username: %s' ), $user->user_login ) . '<br />' . __ ( 'Password' ) . ': ' . $user_pass . '</p>
                    <p>Чтобы установить новый пароль, перейдите по ссылке: ' . wp_login_url ( home_url () . '/login/' ) . '&action=lostpassword</p>';
} else {
	$passwordMsg = '';
}

$message = '<div style="font: 14px/20px Arial; padding: 15px 20px;max-width: 960px; margin: 0 auto;" >
                <div style="text-align: center">
                    <a href="kabacademy.com">
                        <img
				src="http://kabacademy.com/wp-content/uploads/2015/04/logo_top.png">
                        </a>
                    </div>
                    <div>
                        <p>' . $user->display_name . ', здравствуйте!</p>
                        <p>Вы зарегистрированы на сайте Международной академии каббалы,
				крупнейшем в мире учебно-образовательном, бесплатном и
				неограниченном источнике достоверной информации о науке каббала.</p>' . $passwordMsg . '<p><br><br>Позвольте рассказать вам о том, как пользоваться сайтом
				академии, чтобы вы могли использовать все возможности предлагаемого
				обучения.</p>
                        <p>На сайте представлены различные курсы на выбор:</p>
                    </div>
                    <h2>Курсы дистанционного обучения</h2>
                    <div class="clearfix">
                        <img
				src="https://lh6.googleusercontent.com/3qa89pVx0stm8LMi1k3NGDbG4uRVvpx2OQ81tBCH3vTuU8irsOw94HMtb9_Rkz8U0HX7pFh29ao4MyILkRM8b3NE1XfkCJToQLlAdm4HxhuioYw3Q8zjOR1pzYyLkm6i4krLKFnK"
				style="	float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 160px;" alt="" />
                        <p>Онлайн-курс 
                            <span style="color: #00adef;font: bold 16px Arial;">“Основы каббалы.”</span> - новый опыт в вашей жизни от самой природы. На курсе вы
				узнаете о строении мироздания, о силах, управляющих нашей природой,
				нами и всем окружающим.</p>
                        <p>Аттестованные преподаватели в удобное время проведут вас по
				основам каббалистической мудрости. При желании, в процессе вы
				сможете перейти с онлайн обучения на 
                            <a href="http://kabacademy.com/filialyi/">очные курсы</a>.			
                        </p>
                    </div>
                    <div style="text-align: center">
                        <a href="http://kabacademy.com/online-course-lp/" style="display: inline-block;padding: 10px 15px;font: 16px/30px Arial;border-radius: 2px;background-color: #e0922f;text-decoration: none;text-transform: uppercase;cursor: pointer;white-space: nowrap;color: #ffffff;"> 
					Записаться на курс 
			</a>
                    </div>
                    <div class="clearfix">
                        <p>
                            <strong>Об этом курсе:</strong>
                        </p>
                        <ul style="list-style-position: inside;padding-left: 10px;">
                            <li>Базовое обучение — 10 недель</li>
                            <li>Занятия 2 раза в неделю
				</li>
                            <li>Бесплатный доступ к оригинальным текстам</li>
                            <li>Возможность участия в онлайн-сообществе</li>
                            <li>Архив уроков в режиме свободного скачивания</li>
                        </ul>
                    </div>
                    <div class="clearfix">
                        <img
				src="https://lh3.googleusercontent.com/Ao4U-FoLaE6sY-Dztiv2OIqhK00_uCg2890qB-Z2aNSPRLlX6KWxyITNYc80mRNaA6Dl6e8X-bq8IIPdOt2uaz5gDSGCpoHhd2EyE2Mec9j4LY8xAlA6JOoJQdgcIBEXiZh166Gg"
				style="	float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 160px;" alt="" />
                        <p>
                            <span  style="color: #00adef;font: bold 16px Arial;">Вебинар Михаэля Лайтмана</span> (входит в
				программу курса “Основы каббалы.”)
			
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
					src="https://lh6.googleusercontent.com/kwtIAsnJf6hRqfYoxZ99Lo7aiULZflJFORFcItLXJSEMzhy5yS7roceuJbAZwyJieJEfW_LYF_XXvxTXXbRbeAS26HLEP2orL6CK95Q2u_etBLgbSjt8eJTQMNxgd3iII6539bZt"
					style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt="" />
                            <p>
                                <a href="http://kabacademy.com/course/vvodnyj/">
                                    <strong>
							Вводный курс </strong>
                                </a>
                                <br /> Все, что человек сумел узнать и понять о себе и о мире … 
                                <br />
                                <a href="http://kabacademy.com/course/vvodnyj/">Программа курса
						>></a>
                            </p>
                        </div>
                        <div style="display: inline-block;width: 49%;vertical-align: top;">
                            <img
					src="https://lh4.googleusercontent.com/rYAMsLnk_e21ltrBUWBZCqUIMb8MMYXXG8NtCBr15KJz7PZN5lb8Yd8zhHozx5RGuNo-3Q6nJqbwzh86C3OxKjOXBPWIYeMmpH_7a7jDy_E_7gy5W22unVLRpf1Yxi_0lXuhU1LM"
					style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt="" />
                            <p>
                                <a
						href="http://kabacademy.com/course/4-kabbalisticheskaya-teoriya-razvitiya-mira/">
                                    <strong> Теория развития мира </strong>
                                </a>
                                <br /> Процесс образования материи нашего мира, причины появления
					первого живого организма. 
                                <br />
                                <a
						href="http://kabacademy.com/course/4-kabbalisticheskaya-teoriya-razvitiya-mira/">Программа
						курса >></a>
                            </p>
                        </div>
                        <div style="display: inline-block;width: 49%;vertical-align: top;">
                            <img
					src="https://lh5.googleusercontent.com/CvvloU_A0TWi_P9tY5clwpR24jBcDArl-wLECP_BsA-VQVTb4FTk2rDWQvcwiHf4bRVwROSpEIOUU_RY8-eRErNW5FJYPJwahIwAAQcc39A18stqecXrb9KL_3HSW_rydUg42nyy"
					style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt="" />
                            <p>
                                <a href="http://kabacademy.com/course/8-kabbala-i-religiya/">
                                    <strong>
							Каббала и религия </strong>
                                </a>
                                <br /> Сравнительный анализ науки каббала и религии. 
                                <br />
                                <a
						href="http://kabacademy.com/course/8-kabbala-i-religiya/">Программа
						курса >></a>
                            </p>
                        </div>
                        <div style="display: inline-block;width: 49%;vertical-align: top;">
                            <img
					src="https://lh6.googleusercontent.com/6CKDxz4byCEdcbOmvdvWvStugAxN7r3BlQtCryTv6c8pF4hZznXJQkQiMX63WD-mHBoZKeq0HYHMXufhkJV-_AcXLc2l2ppAfFILJVrxGAB9Ys26ZJoYdqxLODgqkRh4h19P47gX"
					style="float: left;margin: 15px 10px 10px 0;border: 0 none;outline: none;width: 100px;" alt="" />
                            <p>
                                <a href="http://kabacademy.com/course/3-vospriyatie-realnosti/">
                                    <strong> Восприятие реальности </strong>
                                </a>
                                <br /> Исследования скрытой части реальности - вне времени,
					движения и пространства. 
                                <br />
                                <a
						href="http://kabacademy.com/course/3-vospriyatie-realnosti/">Программа
						курса >></a>
                            </p>
                        </div>
                    </div>
                    <h2>Очное обучение в филиалах академии</h2>
                    <p>Узнайте, есть ли в вашем городе центр изучения науки каббала.
			Самое интересное и удивительное, чем такая встреча отличается от
			дистанционного обучения, - это знакомство с преподавателями
			Международной академии каббалы, живое общение с единомышленниками и
			быстрый духовный рост.</p>
                    <div style="text-align: center">
                        <a href="http://kabacademy.com/filialyi/"
					style="display: inline-block;padding: 10px 15px;font: 16px/30px Arial;border-radius: 2px;background-color: #e0922f;text-decoration: none;text-transform: uppercase;cursor: pointer;white-space: nowrap;color: #ffffff;"> 
					Карта филиалов </a>
                    </div>
                    <div>
                        <ul style="list-style-position: inside;padding-left: 10px;">
                            <li>онлайн-курсы, помеченные флажком 
                                <span style="color: #00adef;font: bold 16px Arial;">LIVE</span>,
					позволят вам смотреть уроки в реальном времени, а также общаться с
					преподавателями МАК и другими студентами, задавать вопросы и
					получать на них ответы
				
                            </li>
                            <li>курсы для самостоятельного обучения (*) дают возможность
					самим планировать свой график прохождения уроков и выбирать
					наиболее интересующие вас темы</li>
                            <li>вебинары с профессором Михаэлем Лайтманом для тех, кто
					только знакомится с наукой каббала и хочет получить ответы на свои
					вопросы от каббалиста</li>
                        </ul>
                        <div>
                            <em>* Обратите внимание. Некоторые курсы доступны только после
					прохождения вводного курса.</em>
                        </div>
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