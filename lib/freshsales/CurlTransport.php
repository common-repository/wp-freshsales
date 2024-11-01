<?php

/**
 */
class FsalesSmackCurlTransport
{
    /**
     * @var
     */
    private $domain;
    /**
     * @var
     */
    private $appToken;

    /**
     * FsalesSmackCurlTransport constructor.
     * @param $properties
     */
    public function __construct($properties) {
        $this->domain = $properties['domain'];
        $this->appToken = $properties['app_token'];
    }

    /**
     * @param $action
     * @param $message
     * @throws Exception
     */
    public function fsalesPost($action, $message)
    {
        $url = $this->fsalesConstructUrl($action);
        $message['application_token'] = $this->appToken;
        $message['sdk'] = 'php';
        $body = json_encode($message);
        $args = array(
            'headers' => array(
                'Content-type' => 'application/json',
                'Authorization' => 'Token token='.$this->appToken,
                'Content-Length' => strlen($body)
            ),
            'body' => $body
        );
        $result =  wp_remote_post($url, $args );
        $response = wp_remote_retrieve_body($result);
        $http_status = wp_remote_retrieve_response_code($result);
        if ($http_status != 200){
            throw new Exception("Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response);
        }
    }

    /**
     * Construct URL from domain and action
     * @param $action
     * @return string Constructed URL
     */
    private function fsalesConstructUrl($action)
    {
        $url = $this->domain . '/track/'  . $action;
        return $url;
    }



}