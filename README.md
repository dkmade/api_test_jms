Этот вариант реализован с использованием JMS

Заполнять тестовыми данными из миграций - плохая практика, я реализовал это фикстурами, предварительно спарсив данные с
реального сайта (все команды в проекте).

Про пагинацию тоже ничего не сказано. В предыдущих проектах я всегда её делал и вкладывал метаинформацию для пагинации
на фронте. Например, https://усыновите.рф/api/doc в разделе «Получение Детей»

    $ git clone https://github.com/dkmade/api_test_jms.git
    $ cd api_test_jms
    $ docker-compose up -d
    $ docker-compose exec php symfony composer install
    $ docker-compose exec php symfony console d:m:m -n
    $ docker-compose exec php symfony console doctrine:fixtures:load --group=dev

http://127.0.0.1/api/doc  документация

Запустить тесты

    $ docker-compose exec php symfony php bin/phpunit 

Для создания книги можно использовать такой json

    {
      "authors": [
        {
          "id": 1
        }
      ],
      "names": [
        {
          "name": "Название книги на русском",
          "locale": "ru"
        },
        {
          "name": "Name of the book in english",
          "locale": "en"
        }
      ]
    }
