# treasure room front end part

this is the front end part of the treasure room app

by sendind requests towards treasure room's web-service this app permits :

- simple user to get the list of last 3 treasures on the home page
- simple user to get the lis tof all treasures on the inventory page
- logged-in user to the the detail of one particular treasure
- logged-in user to add a treasure in the treasure room

# to set up :

- clone this repository : git clone https://github.com/ClaireV38/checkpoint4_treasure_room.git
- copy the follwing line : .env : DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name. 
paste it into an new ".env.local" file at the root of the project. replace following values as above :
db_user = usual user name for your database
db_password = usual password
db_name = database name (whichever)
- run composer install
- run yarn install
- run yarn encore dev
- run in that order :
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
php bin/console doctrine:fixtures:load





