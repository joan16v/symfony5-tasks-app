# symfony5-tasks-app

Symfony5 Tasks App

A PHP/Symfony5 app, to use it with a MySQL database. Bootstrap for UX.

Use it with Docker.

**docker-compose up**

Useful commands (run at startup):

**docker-compose run website ./composer.phar install**

**docker-compose run website php bin/console cache:clear**

**docker-compose run website php bin/console doctrine:schema:update --force**

**docker-compose run website php bin/console doctrine:query:sql "INSERT INTO tasks_users (login, password, name, admin) VALUES ('admin', '74f92c6ce2d870aa327bf5dfee618076', 'admin', 1);"**

http://localhost:8000

<img src="https://i.imgur.com/dsYNeHe.png">
<img src="https://i.imgur.com/89c9TsA.png">
<img src="https://i.imgur.com/mGEEob1.png">
