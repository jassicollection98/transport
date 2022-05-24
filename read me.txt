Before you run the project =====>
first run you xampp and fire up the localhost.
then create a database named as employees_db.
then follow the below given commands.



How to run???

step 1- Copy the destination folder to the xampp/htdocs folder

step 2- now go to the project folder directory using the command prompt.

step 3- now run composer update --no-scripts (If you have already installed the composer and the laravel)

step 4- php artisan migrate (to migrate the db files)

step 5- php artisan key:generate

step 6- php artisan db:seed

step 7- php serve (to run the from the localhost).