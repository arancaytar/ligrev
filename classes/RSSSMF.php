<?php

namespace Ligrev;

/**
 * A subclass of RSS feed that is requested from an SMF2 forum
 * as a logged-in user.
 */
class RSSSMF extends RSS {
  private $cookies = [];

  public function __construct($config) {
    $config['url'] = "{$config['site']}/{$config['path']}";
    parent::__construct($config);
    $curl = new \Curl\Curl();
    $curl->post($config['site']."/index.php?action=login2", ['user'=>$config['user'], 'passwrd' => $config['pass']]);
    $this->cookies = $curl->getResponseCookies();
  }

  public function _connect() {
    $curl = parent::_connect();
    foreach ($this->cookies as $key => $value) {
      $curl->setCookie($key, $value);
    }
    return $curl;
  }
}
