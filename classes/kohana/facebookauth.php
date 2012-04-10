<?php
/**
 * Facebook Authentication module for Kohana 3.2
 *
 * @package Kohana_FacebookAuth
 * @author Molnár Balázs
 * @copyright (c)2012 Molnár Balázs
 * @website http://github.com/mobal/kohana-facebookauth-module
 * @license http://www.opensource.org/licenses/isc-license.txt
 */

class Kohana_FacebookAuth {
    private $login_url, $logout_url;
    private  $config, $data, $fb, $me, $uid;

    /**
     * Creates a new FacebookAuth object.
     *
     * @return FacebookAuth
     */
    public static function factory() {
        return new FacebookAuth();
    }

    /**
     * Creates a new FacebookAuth object.
     */

    protected function __construct() {
        include Kohana::find_file('vendor', 'facebook');

        // Load configuration "config/facebook"
        $this->config = Kohana::$config->load('facebook');

        // Create new Facebook object
        $this->fb = new Facebook(array(
            'appId' =>  $this->config->appId,
            'secret' =>  $this->config->secret,
            'cookie' =>  $this->config->cookie,
        ));

        try {
            $this->me = $this->fb->api('/' . $this->fb->getUser(), 'GET');
        } catch (FacebookApiException $e) {
            // Do nothing.
        }
    }

    /**
     * Check users login.
     *
     * @return mixed
     */

    public function logged_in() {
        if($this->fb->getUser()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return user id if success, otherwise false.
     *
     * @return bool
     */

    public function user_id() {
        if($this->logged_in()) {
            $this->uid = $this->fb->getUser();

            return $this->uid;
        } else {
            return false;
        }
    }

    /**
     * Returns user data, default in case of failure.
     *
     * @param $key
     * @param null $default
     * @return mixed
     * @throws FacebookApiException
     */

    public function get($key, $default = NULL) {
        if(!$uid = $this->user_id()) {
            $this->login_url();

            throw new FacebookApiException('User is not logged in.');
        } else {
            if(empty($this->data)) {
                $fql_query = array(
                    'method'    =>  'fql.query',
                    'query' =>  'SELECT ' . $this->config->fields . ' FROM user WHERE uid =' . $uid,
                );

                $this->data = $this->fb->api($fql_query);
            }

            if(!empty($this->data[0][$key])) {
                return $this->data[0][$key];
            } else {
                return $default;
            }
        }
    }

    /**
     * Creates a login url, based on scope, redirect_uri and display.
     *
     * @return string
     */

    public function login_url() {
        return $this->login_url = urldecode($this->fb->getLoginUrl(array(
            'scope' =>  $this->config->scope,
            'redirect_uri'  =>  $this->config->redirect_uri,
            'display'   =>  $this->config->display,
        )));
    }

    /**
     * Creates a logout url based on next.
     *
     * @return string
     */

    public function logout_url()  {
        return $this->logout_url = urldecode($this->fb->getLogoutUrl(array(
            'next'  =>  $this->config->next,
        )));
    }
}
