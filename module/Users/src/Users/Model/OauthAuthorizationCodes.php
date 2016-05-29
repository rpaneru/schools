<?php
namespace Users\Model;

class OauthAuthorizationCodes
{
    public $authorization_code;
    public $client_id;
    public $user_id;
    public $redirect_uri;
    public $expires;
    public $scope;
    public $id_token;

    public function exchangeArray($data)
    {
        $this->authorization_code = (isset($data['authorization_code'])) ? $data['authorization_code'] : null;
        $this->client_id = (isset($data['client_id'])) ? $data['client_id'] : null;
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->redirect_uri = (isset($data['redirect_uri'])) ? $data['redirect_uri'] : null;
        $this->expires = (isset($data['expires'])) ? $data['expires'] : null;
        $this->scope  = (isset($data['scope'])) ? $data['scope'] : null; 
        $this->id_token  = (isset($data['id_token'])) ? $data['id_token'] : null; 
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
