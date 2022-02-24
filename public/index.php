<?php
use App\Models\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/db.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
   $response->getBody()->write('Hello World!');
   return $response;
});
$app->get('/user/all', function (Request $request, Response $response) {
    
   $sql = "SELECT * FROM user";
    
   try {
     $db = new Db();
     $conn = $db->connect();
     $stmt = $conn->query($sql);
     $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
     $db = null;
     echo "succsessfully";
     $response->getBody()->write(json_encode($customers));
     return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(200);
   } catch (PDOException $e) {
     $error = array(
       "message" => $e->getMessage()
     );
  
     $response->getBody()->write(json_encode($error));
     return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(500);
   }
  
  });

  $app->get('/user/id/{id}', function (Request $request, Response $response,array $args) {
   
   $sql = "SELECT * FROM user where id_card=".$args['id'];
    
   try {
     $db = new Db();
     $conn = $db->connect();
     $stmt = $conn->query($sql);
     $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
     $db = null;
     echo "succsessfully";
     $response->getBody()->write(json_encode($customers));
     return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(200);
   } catch (PDOException $e) {
     $error = array(
       "message" => $e->getMessage()
     );
  
     $response->getBody()->write(json_encode($error));
     return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(500);
   }
});
 
$app->post('/user/add', function (Request $request, Response $response, array $args) {
   $data = $request->getParsedBody();
   $id=$data['id_card'];
   $name = $data["username"];
   $email = $data["email"];
   $phone = $data["phone"];
  
   $sql = "INSERT INTO user (id_card,username, email, phone) VALUES (:id_card,:name, :email, :phone)";
  
   try {
     $db = new Db();
     $conn = $db->connect();
    
     $stmt = $conn->prepare($sql);
     $stmt->bindParam(':id_card', $id);
     $stmt->bindParam(':name', $name);
     $stmt->bindParam(':email', $email);
     $stmt->bindParam(':phone', $phone);
  
     $result = $stmt->execute();
  
     $db = null;
     $response->getBody()->write(json_encode($result));
     return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(200);
   } catch (PDOException $e) {
     $error = array(
       "message" => $e->getMessage()
     );
  
     $response->getBody()->write(json_encode($error));
     return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(500);
   }
  });
/*
$app->put('/user/update/{id}', function (Request $request, Response $response, array $args) {
$id = $request->getAttribute('id');
$data = $request->getParsedBody();
$name = $data["username"];
$email = $data["email"];
$phone = $data["phone"];

$sql = "UPDATE user SET
          username = :name,
          email = :email,
          phone = :phone
WHERE id =$id ";

try {
  $db = new Db();
  $conn = $db->connect();
 
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':phone', $phone);

  
  $result = $stmt->execute();

  $db = null;
  echo "Update successful! ";
  $response->getBody()->write(json_encode($result));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
} catch (PDOException $e) {
  $error = array(
    "message" => $e->getMessage()
  );

  $response->getBody()->write(json_encode($error));
  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(500);
}
});

*/
$app->run();