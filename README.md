# RAMPAGE CMS 
Открытая, бесплатная система для создания веб-сайтов, наполнения и управления контентом.
 
### История создания
 
Подробно про историю создания можно прочитать [здесь](http://www.therampage.org/rampage-cms).

### Системные требования  
	PHP 5.4
	Apache 2  
	MySQL 5 
	Composer 2.2.10  
	ZendFramework 1.12.20
	PHP Unit 4.0.0 
 
### Установка

В директории **docker-setup** находятся все конфигурационные файлы для сборки и запуска рабочего окружения.
Для начала нам нужно собрать веб-сервер, для этого перейдите в директорию:
    
    docker-setup
    
и выполните команду:

    docker build .
 
> Обратите внимание: в процессе работы скрипта на шаге 10 может возникнуть ошибка ERROR [10/24] связанная с GPG. Это обычно связано с недоступностью стороннего сервера. Можно подождать и повторить запуск команды. Если не помогает, поменяйте сервер GPG в **Dockerfile**: gpg --keyserver keyserver.ubuntu.com --recv-keys "$key"; \
 
Когда все шаги буду и успешно исполнены нужно запустить окружение.  Для этого выполните команду:

    docker compose up
  
После успешного завершения предыдущей команды пропишите доменное имя хоста на локальной машине. В Linux/MacOS файл называется:

    /etc/hosts
  
В Windows:

    windows/system32/drivers/etc/hosts
  
Добавьте в него строку:

    127.0.0.1 rampagecms 
  
После чего откройте браузер и перейдите по адресу: [http://rampagecms](http://rampagecms)

