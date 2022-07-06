

``laravel new example-app``

``cd example-app``

``php artisan serve``

- Engadir brezze

``composer require laravel/breeze --dev``

``php artisan breeze:install``

``npm install``
``npm run dev``

<small>(por defecto breeze instala un scaffolding con tailwindcss a traves de @vite / cambiar a Mix)</small>

``php artisan migrate``

![primeira migración](C:\laragon\www\laobrezz\assets\image-20220705110650373.png)

- Engadir primeiro usuario.

  ``php artisan make:seeder CreateUsersSeeder``

  Engadir o modelo  no seeder:

  ``use App\Models\User;``

  Completar o seeder:

  ```php
  <?php
  
  namespace Database\Seeders;
  
  use Illuminate\Database\Seeder;
  
  use App\Models\User;
  
  class CreateUsersSeeder extends Seeder
  {
      /**
       * Run the database seeds.
       *
       * @return void
       */
      public function run()
      {
          // aquí os datos do primeiro usuario
          $user = [
  
              [
  
                  'name'=>'admin',
  
                  'email'=>'admin@gmail.com',
  'is_admin' => '1',
                  'password'=> bcrypt('123456'),
                  
  
              ],
  
              [
  
                  'name'=>'user',
  
                  'email'=>'user@gmail.com',
  'is_admin' => '0',
                  'password'=> bcrypt('123456'),
  
              ],
  
          ];
  
  
  
          foreach ($user as $key => $value) {
  
              User::create($value);
  
          }
  
      }
  }
  
  ```

  

  ### Superadmin

- 

- 

  

- Modificar taboa users &rarr; engadir booleano ``is_admin``.







https://blog.devgenius.io/basic-laravel-admin-panel-basic-laravel-crud-creation-for-permission-management-6bd93fb0e1a2





https://www.autoscripts.net/role-acces-login-in-laravel/#role-acces-login-in-laravel
