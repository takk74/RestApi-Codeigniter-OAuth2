<?php

namespace App\Controllers;

use App\Libraries\CiOAuth;
use OAuth2\Request;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    protected $ci_oauth;
    protected $oauth_request;
    protected $oauth_respond;

    use ResponseTrait;

    public function __construct()
    {
        $this->ci_oauth = new CiOAuth();
        $this->oauth_request = new Request();
    }

    public function login() {
        $this->ci_oauth->server->setConfig('acces_lifetime' , 216000);
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
