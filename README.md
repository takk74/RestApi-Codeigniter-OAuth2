# **APIREST Codeigniter 4 + OAuth2** :blush:


![](https://i.imgur.com/6PrgzK1.png)

# **CREATION DU PROJET** :star:

## Créer un projet code igniter via composer :exclamation:
composer create-project codeigniter4/appstarter tuto-api-oauth

### 1/ Acceder à votre dossier 
cd tuto-api-oauth
### 2/ Lancer votre projet 
php spark serve

### 3/ Aller dans app/Config/Boot/development.php et app/Config/Boot/.php et changez le display_errors valeur à 1 plutôt que 0. :no_good:
ini_set('display_errors', '1');

### 5/ Créer une base de donnée local dans phpMyAdmin
CREATE DATABASE tuto-api-oauth

### 6/ A l'intérieur de votre base de donnée , créer une nouvelle table employés ou toutes les données seront stockés
```sql
CREATE TABLE employees (
    id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
    name varchar(100) NOT NULL COMMENT 'Name',
    email varchar(255) NOT NULL COMMENT 'Email Address',
    PRIMARY KEY (id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='datatable demo table' AUTO_INCREMENT=1;

INSERT INTO `employees` (`id`, `name`, `email`) VALUES
(1, 'John Doe', 'john@gmail.com'),
(2, 'Vanya Hargreeves', 'vanya@gmail.com'),
(3, 'Luther Hargreeves', 'luther@gmail.com'),
(4, 'Diego Hargreeves', 'diego@gmail.com'),
(5, 'Klaus Hargreeves', 'klaus@gmail.com'),
(6, 'Ben Hargreeves', 'ben@gmail.com'),
(7, 'The Handler', 'handler@gmail.com');
```

### 7/ Pour connecter votre base de données , aller dans application/config/database.php et changer hostname/username/password/database en fonction de vos informations
```
public $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'test',
        'password' => '4Mu99BhzK8dr4vF1',
        'database' => 'demo',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => (ENVIRONMENT !== 'development'),
        'cacheOn'  => false,
        'cacheDir' => '',
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
	];
```

### 8/ Modifier votre hostname en fonction de votre serveur
#### MAMP
```
public $default = [
  ...
     'hostname' => '/Applications/MAMP/tmp/mysql/mysql.sock',
  ...
]
```
#### XAMPP
```
public $default = [
  ...
     'hostname' => '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
  ...
]
```
## Créer un modèle :exclamation:
### Créer un fichier de modèle EmployeeModel.php dans le dossier Models. 
Placez le code suivant dans le fichier pour définir le modèle.
```
<?php 
namespace App\Models;
use CodeIgniter\Model;

class EmployeeModel extends Model {

    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email'];
}
```

### Créer un contrôleur RestController.php dans le dossier Controllers , Dans ce fichier, nous allons créer les fonctions qui géreront sans relâche les opérations de création, de lecture, de mise à jour et de suppression

```
<?php 
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\EmployeeModel;

class RestController extends ResourceController {

    use ResponseTrait;

    public function index()
      $model = new EmployeeModel();
      $data['employees'] = $model->orderBy('id', 'DESC')->findAll();
      return $this->respond($data);
    
    public function create() 
        $model = new EmployeeModel();
        $data = [
            'name' => $this->request->getVar('name'),
            'email'  => $this->request->getVar('email'),
        ];
        $model->insert($data);
        $response = [
          'status'   => 201,
          'error'    => null,
          'messages' => [
              'success' => 'Employee created successfully'
          ]
      ];
      return $this->respondCreated($response);
    
    public function getEmployee($id = null)
        $model = new EmployeeModel();
        $data = $model->where('id', $id)->first();
        if($data)
            return $this->respond($data);
        else
            return $this->failNotFound('No employee found');
        
    
    public function update($id = null)
        $model = new EmployeeModel();
        $id = $this->request->getVar('id');
        $data = [
            'name' => $this->request->getVar('name'),
            'email'  => $this->request->getVar('email'),
        ];
        $model->update($id, $data);
        $response = [
          'status'   => 200,
          'error'    => null,
          'messages' => [
              'success' => 'Employee updated successfully'
          ]
      ];
      return $this->respond($response);
    
    public function delete($id = null)
        $model = new EmployeeModel();
        $data = $model->where('id', $id)->delete($id);
        if($data)
            $model->delete($id);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Employee successfully deleted'
                ]
            ];
            return $this->respondDeleted($response);
        else
            return $this->failNotFound('No employee found');

```
#### Ces 5 fonctions basiques ont toutes une utilité bien défini

index () - Récupère tous les enregistrements de la base de données.
create () - Il propulse un enregistrement d'employé dans la table de base de données.
show () - Il obtient un seul enregistrement d'employé de la base de données.
update () - Il met à jour l'enregistrement utilisateur dans la base de données.
delete () - Il supprime un enregistrement d'employé dans la base de données.

## Création des routes :exclamation:
### Ouvrez le app/Config/Routes.php et recherchez le code suivant.

```
$Route->get('/', 'home::index');
```
Cette ligne renvoie la fonction index du controller home qui lui même retourne la vue de la page d'accueil, vous puvez la supprimer

### Inserer cette ligne à la place
```
$routes->resource('RestController');
```
Vous avez désormais accès à vos données via l'url , ici on utilisera Postman pour tester cette api.


### Récuperer tous les employés | GET
http://localhost:8080/employee

![](https://i.imgur.com/4BySgw0.png)

### Récuperer un employé précis | GET
http://localhost:8080/employee/1
![](https://i.imgur.com/CZImtfF.png)

### Mettre à jour un employé précis | PUT
http://localhost:8080/employee/1
![](https://i.imgur.com/sjsZl9m.png)

### Ajouter un nouvel employé | POST
http://localhost:8080/employee
![](https://i.imgur.com/tOgsVY8.png)

### Supprimer un employé | DELETE
http://localhost:8080/employee/1

![](https://i.imgur.com/NBXjQvE.png)


# **Installation de OAuth2** :star:
Executer ce code dans le terminal de votre projet pour installer OAuth2.
```
composer.phar require bshaffer/oauth2-server-php "^1.10"
```
## Ajouter ces tables dans votre base de données
```
CREATE TABLE oauth_clients (
  client_id             VARCHAR(80)   NOT NULL,
  client_secret         VARCHAR(80),
  redirect_uri          VARCHAR(2000),
  grant_types           VARCHAR(80),
  scope                 VARCHAR(4000),
  user_id               VARCHAR(80),
  PRIMARY KEY (client_id)
);

CREATE TABLE oauth_access_tokens (
  access_token         VARCHAR(40)    NOT NULL,
  client_id            VARCHAR(80)    NOT NULL,
  user_id              VARCHAR(80),
  expires              TIMESTAMP      NOT NULL,
  scope                VARCHAR(4000),
  PRIMARY KEY (access_token)
);

CREATE TABLE oauth_authorization_codes (
  authorization_code  VARCHAR(40)     NOT NULL,
  client_id           VARCHAR(80)     NOT NULL,
  user_id             VARCHAR(80),
  redirect_uri        VARCHAR(2000),
  expires             TIMESTAMP       NOT NULL,
  scope               VARCHAR(4000),
  id_token            VARCHAR(1000),
  PRIMARY KEY (authorization_code)
);

CREATE TABLE oauth_refresh_tokens (
  refresh_token       VARCHAR(40)     NOT NULL,
  client_id           VARCHAR(80)     NOT NULL,
  user_id             VARCHAR(80),
  expires             TIMESTAMP       NOT NULL,
  scope               VARCHAR(4000),
  PRIMARY KEY (refresh_token)
);

CREATE TABLE oauth_users (
  username            VARCHAR(80),
  password            VARCHAR(80),
  first_name          VARCHAR(80),
  last_name           VARCHAR(80),
  email               VARCHAR(80),
  email_verified      BOOLEAN,
  scope               VARCHAR(4000),
  PRIMARY KEY (username)
);

CREATE TABLE oauth_scopes (
  scope               VARCHAR(80)     NOT NULL,
  is_default          BOOLEAN,
  PRIMARY KEY (scope)
);

CREATE TABLE oauth_jwt (
  client_id           VARCHAR(80)     NOT NULL,
  subject             VARCHAR(80),
  public_key          VARCHAR(2000)   NOT NULL
);
```

### Création d'un client dans oauth_clients
Executer ce code dans votre base de donnée
```
INSERT INTO oauth_clients (client_id, client_secret, grant_types , scope)
VALUES ('testclient', 'testpassword', 'password' , 'tuto-api-oauth' )
```

### Création d'un user dans oauth_users
Executer ce code dans votre base de donnée
```
INSERT INTO `oauth_users`(`username`, `password`, `first_name`, `last_name`, `email`, `email_verified`, `scope`) VALUES ('musky','secret','elon','musk','elon.musk@gmail.com','1','tuto-api-oauth')
```
### Crypter le mot de passe de l'utilisateur
Executer ce code dans votre base de donnée
```
UPDATE `oauth_users` SET `password` = SHA1('secrete')
```

### Créer un fichier CiOAuth.php dans le dossier libraries
```
<?php

namespace App\Libraries;

use OAuth2\GrantType\UserCredentials;
use OAuth2\Server;
use OAuth2\Storage\Pdo as StoragePdo;

class CiOAuth {
    public $server;
    protected $storage;
    protected $dsn;
    protected $db_username;
    protected $db_password;

    public function __construct()
    {
        $this->dsn = 'mysql:dbname=' . getenv('database.default.database') . ';host=' . getenv('database.default.hostname') . '';
        $this->db_username = getenv('database.default.username');
        $this->db_password = getenv('database.default.password');
    }

    public function initialize() 
    {
        $this->storage = new StoragePdo([
            'dsn' => $this->dsn,
            'username' => $this->db_username,
            'password' => $this->db_password

        ]);
       
        $this->server = new Server($this->storage);
        $this->server->addGrantType(new UserCredentials($this->storage));
    }
}


?>
```

### Créer ou mettez à jour le fichier Home.php dans le dossier Controllers
```
<?php

namespace App\Controllers;

use App\Libraries\CiOAuth;
use OAuth2\Request;

class Home extends BaseController
{
    protected $ci_oauth;
    protected $oauth_request;
    protected $oauth_respond;

    public function __construct()
    {
        $this->ci_oauth = new CiOAuth();
        $this->oauth_request = new Request();
    }

    public function login() {
        $this->oauth_respond = $this->ci_oauth->server->handleTokenRequest(
            $this->oauth_request->createFromGlobals()
        );

        $code = $this->oauth_respond->getStatusCode();
        $body = $this->oauth_respond->getResponseBody();

        return $this->genericResponse($code , $body);
    }

   protected function genericResponse($code , $body) {
       if($code == 200) {
            return $this->respond([
                'code' => $code,
                'body' => json_decode($body),
                'authorised' => $code
            ]);
       } else {
            return $this->fail(json_decode($body));
       }
   }
}

```

### Mettre à jour routes.php
```
$routes->resource('Employee' , ['filter' => 'auth']);
```

# **Test avec postman** :star:
#### Choisir Basic Auth comme type de connexion et rentrer les informations de la table oauth_clients

![](https://i.imgur.com/4Jtmagz.png)

## Remplir le body avec le grant_type , le username et le password de la table oauth_users
#### Une fois la requete POST envoyé , un acces token est renvoyé pour nous permettre d'acceder à toutes nos routes via un bearer token.

![](https://i.imgur.com/nogSUnT.png)


### Bravo à vous , vous avez désormais une API REST totalement sécurisé et prête à être en ligne. :+1:


![](https://i.imgur.com/XvxNJsP.png)
