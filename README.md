php artisan make:migration create_users_table --create=users

php artisan migrate // executar as migrações pendentes no banco de dados

php artisan migrate:rollback
// desfazer a última migration

php artisan migrate:refresh
// reseta e reaplica todas do zero, útil em desenvolvimento para reconstruir o esquema rapidamente.

php artisan make:model Post
// criar um model 



facade