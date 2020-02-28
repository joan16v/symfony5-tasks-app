# symfony5-tasks-app

A PHP/Symfony5 app, to use it with a MySQL database. Bootstrap for UX.

Use it with Docker.

**docker-compose up**

Useful commands (run at startup):

**docker-compose run website ./composer.phar install**

**docker-compose run website php bin/console doctrine:schema:update --force**

**docker-compose run website php bin/console doctrine:query:sql "INSERT INTO tasks_users (login, password, name, admin) VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 1);"**

http://localhost:8000

user: admin

password: admin

<img src="https://i.imgur.com/dsYNeHe.png">
<img src="https://i.imgur.com/89c9TsA.png">
<img src="https://i.imgur.com/mGEEob1.png">
