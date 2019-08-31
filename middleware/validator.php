<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

use MrShan0\PHPFirestore\FirestoreClient;

class validator
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
      $firestoreClient = new FirestoreClient(getenv('FIREBASE_PROJECT_ID'), getenv('FIREBASE_APIKEY'), [
        'database' => getenv('FIREBASE_DATABASEURL'),
      ]);

      $serviceAccount = ServiceAccount::fromJsonFile(getenv('GOOGLE_APPLICATION_CREDENTIALS'));

      $data_body = json_decode($request->getBody(), true);
    
      $email = $data_body['email'] ? $data_body['email'] : '';
      $password = $data_body['password'] ? $data_body['password'] : '';
    
      if ($email == '' || $password == '') {
        $fail = array(
          'message' => 'Los parametros estan incompletos, revise la informacion suministrada antes de enviar nuevamente.',
          'reason'=> 'missing parameters'
        );
    
        $fail = json_encode($fail);
    
        $response->getBody()->write($fail);
        return $response;
      }

      $firestoreClient
        ->authenticator()
        ->signInEmailPassword($email, $password);
    
      $authToken = $firestoreClient->authenticator()->getAuthToken();
    
      
      $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->create();
    
      $auth = $firebase->getAuth();
      
      try {
        $user = $auth->verifyPassword($email, $password);
        $user = json_encode($user);
    
        $emailVerified = json_decode($user, true)['emailVerified'];
        $uid = json_decode($user, true)['uid'];
    
        if (!$emailVerified) {
          $fail = array(
            'message' => 'Debe validar su correo para poder hacer uso de esta cuenta.',
            'reason'=> 'not-verified'
          );
      
          $fail = json_encode($fail);
      
          $response = new Response();
          $response->getBody()->write($fail);
          return $response;
        }
      } catch (Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
          $error = $e->getMessage();
          $response->getBody()->write("Error autenticando: $error");
          return $response;
      }
    
      try {
        $verifiedIdToken = $firebase->getAuth()->verifyIdToken($authToken);
      } catch (InvalidToken $e) {
        $fail = array(
          'message' => 'El token es incorrecto.',
          'reason' => 'incorrect-token'
        );
        $fail = json_encode($fail);
    
        $response->getBody()->write($fail);
        return $response;
      }
      // ->withAttribute(self::class, $uid)
      $response = $handler->handle($request->withAttribute('uid', $uid));
      $existingContent = (string) $response->getBody();

      $response = new Response();
      $response->getBody()->write($existingContent);
      return $response->withHeader('Content-Type', 'application/json');
    }
}