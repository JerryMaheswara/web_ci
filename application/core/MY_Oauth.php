<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Oauth extends MY_Controller
{
    /*****************************************************************************/
    /*****************************************************************************/

    public function __construct()
    {
        parent::__construct();

        $config = (array) $this->load->database('oauth', true);
        // opn(sha1('pass'));exit();
        // opn($config);exit();
        // opn(get_included_files());exit();
        // $config2['user_table'] = 'user';

        // OAuth2\Autoloader::register();
        // $this->storage  = new OAuth2\Storage\Pdo($config );
        $this->storage = new OAuth2\Storage\Pdo(array('dsn' => $config["dsn"], 'username' => $config["username"], 'password' => $config["password"]));
        // opn($this->storage);exit();

        // $this->server   = new OAuth2\Server($this->storage);
        $this->server   = new OAuth2\Server($this->storage, array('allow_implicit' => false));
        $this->request  = OAuth2\Request::createFromGlobals();
        $this->response = new OAuth2\Response();
        // opn($_COOKIE);exit();
        // session_destroy();
        // opn($this->config);exit();

    }
    /*****************************************************************************/
    /*****************************************************************************/

    /**
     * client_credentials, for more see: http://tools.ietf.org/html/rfc6749#section-4.3
     * @link http://homeway.me/2015/06/29/build-oauth2-under-codeigniter/#Client_Credentials
     */
    /*****************************************************************************/
    public function client_credentials()
    {
        $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage, array(
            "allow_credentials_in_request_body" => true,
        )));
        $this->server->handleTokenRequest($this->request)->send();
    }
    /**
     * password_credentials, for more see: http://tools.ietf.org/html/rfc6749#section-4.3
     * @link http://homeway.me/2015/06/29/build-oauth2-under-codeigniter/#Resource_Owner_Password_Credentials
     */
    /*****************************************************************************/
    public function password_credentials($param = null)
    {
        // $users   = array("username" => array("password" => 'password', 'first_name' => 'xiaocao', 'last_name' => 'grasses'));
        // $users['username']['password']   = 'password';
        // $users['username']['first_name'] = 'xiaocao';
        // $users['username']['last_name']  = 'grasses';

        $users[$param['username']]['password']   = $param['password'];
        $users[$param['username']]['first_name'] = $param['first_name'];
        $users[$param['username']]['last_name']  = $param['last_name'];
        $str = new OAuth2\Storage\Memory(array('user_credentials' => $users));
        $this->server->addGrantType(new OAuth2\GrantType\UserCredentials($str));
        $this->server->handleTokenRequest($this->request)->send();
    }
    /**
     * refresh_token, for more see: http://tools.ietf.org/html/rfc6749#page-74
     */
    /*****************************************************************************/
    public function refresh_token()
    {
        $this->server->addGrantType(new OAuth2\GrantType\RefreshToken($this->storage, array(
            "always_issue_new_refresh_token" => true,
            "unset_refresh_token_after_use"  => true,
            "refresh_token_lifetime"         => 2419200,
        )));
        $this->server->handleTokenRequest($this->request)->send();
    }
    /**
     * limit scpoe here
     * @param $scope = "node file userinfo"
     */
    /*****************************************************************************/
    public function require_scope($scope = "")
    {
        if (!$this->server->verifyResourceRequest($this->request, $this->response, $scope)) {
            $this->server->getResponse()->send();
            die();
            // echo json_encode(array('failed' => true, 'message' => 'token exired'));
        }
    }
    /*****************************************************************************/
    public function check_client_id()
    {
        if (!$this->server->validateAuthorizeRequest($this->request, $this->response)) {
            $this->response->send();
            die;
        }
    }
    /*****************************************************************************/
    public function authorize($is_authorized)
    {
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));
        $this->server->handleAuthorizeRequest($this->request, $this->response, $is_authorized);
        if ($is_authorized) {
            $code = substr($this->response->getHttpHeader('Location'), strpos($this->response->getHttpHeader('Location'), 'code=') + 5, 40);
            header("Location: " . $this->response->getHttpHeader('Location'));
        }
        $this->response->send();
    }
    /*****************************************************************************/
    public function authorization_code()
    {
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));
        $this->server->handleTokenRequest($this->request)->send();
    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
}

/* End of file MY_Oauth.php */
/* Location: ./application/core/MY_Oauth.php */
