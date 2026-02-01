<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Service extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
  /**
   * Instance of the main Request object.
   *
   * @var CLIRequest|IncomingRequest
   */
  protected $request;

  /**
   * An array of helpers to be loaded automatically upon
   * class instantiation. These helpers will be available
   * to all other controllers that extend BaseController.
   *
   * @var list<string>
   */
  protected $helpers = [];

  /**
   * Be sure to declare properties for any property fetch you initialized.
   * The creation of dynamic property is deprecated in PHP 8.2.
   */
  // protected $session;
  public $db;
  public $get;
  public $post;
  public $session;
  public $redis;
  public $forge;
  public $cache;

  public function __construct(){

//    $db = db_connect();
    $db = \Config\Database::connect();
    $request = \Config\Services::request();
    $session = \Config\Services::session();

    $this->db = $db;
    $this->get = $request->getGet();
    $this->post = $request->getPost();
    $this->session = $session;
    $this->forge = \Config\Database::forge();
    $this->cache = service('cache');
    $this->accessLog();




  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  /**
   * @return void
   */
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
      // Do Not Edit This Line
      parent::initController($request, $response, $logger);

      // Preload any models, libraries, etc, here.

      // E.g.: $this->session = service('session');
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function randNum($length) {
    $result = '';
    for ($i = 0; $i < $length; $i++) {
      $result .= rand(0, 9);
    }
    return $result;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function checkLogin()
  {

    if(!empty($this->session->get('login'))){
      return true;
    }
    else{
      header('Location: /login');
    }


  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function accessLog($response = '')
  {

    $info = [
              'type'         => $_SERVER['REQUEST_METHOD'],
              'post'         => json_encode($_POST),
              'page'         => $_SERVER['REQUEST_URI'],
              'ipAddress'    => $_SERVER['REMOTE_ADDR'],
              'agent'        => $_SERVER['HTTP_USER_AGENT'],
              'response'     => json_encode($response),
            ];

//    print_r($_SERVER);
//    die();

    log_message('info', 'type: {type} post: {post} page: {page} ip: {ipAddress} agent: {agent} response: {response}', $info);

  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function checkUserPermission()
  {
    $user = $this->session->get('login');

    if (!empty($user)) {
      if (strpos($_SERVER['REQUEST_URI'], '?') === false) {
        $currentPage = $_SERVER['REQUEST_URI'];
      }
      else {
        $currentPage = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
      }

      if (strpos($user['pages'],  $currentPage) === false && strpos($_SERVER['REQUEST_URI'], '/model/data') === false && $user['pages'] != '*') {
//      print_r($user['pages']);
//      die();
        header('Location: /noPermission');
      }

      if (strpos($_SERVER['REQUEST_URI'], '/model/data') !== false) {
        $currentModel = substr($_SERVER['REQUEST_URI'], strripos($_SERVER['REQUEST_URI'], '/') + 1);
//      print_r($user['models']);
//      die();
        if (strpos($user['models'],  $currentModel) === false && $user['models'] != '*') {
          header('Location: /noPermission');
        }
      }
    }
    else {
      return true;
    }

  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function staticData()
  {
    $res = ['menus' => json_decode($this->cache->get('models'),true)];
    $res['systemConfig'] = json_decode($this->cache->get('systemConfig'), true);
    $res['sqlVersion'] = $this->db->query("select VERSION() as sqlVersion")->getRowArray();
    $res['users'] = json_decode($this->cache->get('users'),true);
    return $res;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

}
