<?php

/**
 */

require(__DIR__ . '/CurlTransport.php');

/**
 * Class Client
 */
class FsalesSmackClient
{

    /**
     * @var CurlTransport
     */
    private $curlTrans;

    /**
     * Client constructor.
     * @param $properties
     */
    public function __construct($properties)
    {
        $this->curlTrans = new FsalesSmackCurlTransport($properties);
    }

    /**
     * fsalesIdentify user
     * @param array $properties
     * @throws Exception
     */
    public function fsalesIdentify(array $properties)
    {
        $message = array();
        $message['identifier'] = $properties['identifier'];
        // unset identifier from properties
        unset($properties['identifier']);
        $message['visitor'] = $this->fsalesConvertArraytoObj($properties);
        // post message
        $this->curlTrans->fsalesPost('visitors', $message);
    }

    /**
     * Track Event
     * @param array $properties
     * @throws Exception
     */
    public function fsalesTrackEvent(array $properties)
    {
        $message = array();
        $message['identifier'] = $properties['identifier'];
        // unset identifier from properties
        unset($properties['identifier']);
        $message['event'] = $this->fsalesConvertArraytoObj($properties);
        // post message
        $this->curlTrans->fsalesPost('events', $message);
    }

    /**
     * Track Page View
     * @param array $properties
     * @throws Exception
     */
    public function fsalesTrackPageView(array $properties)
    {
        $message = array();
        $pageView = new stdClass();
        $pageView->url = $properties['url'];
        $message['identifier'] = $properties['identifier'];
        // unset identifier from properties
        unset($properties['identifier']);
        $message['page_view'] = $pageView;
        // post message
        $this->curlTrans->fsalesPost('page_views', $message);
    }

    /**
     * Utility method to convert array members to an object
     * @param array $prop
     * @return stdClass
     */
    private function fsalesConvertArraytoObj(array $prop){
        $object = new stdClass();
        foreach ($prop as $key => $value)
        {
            $object->$key = $value;
        }
        return $object;
    }

}