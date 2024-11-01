<?php

/**
 */

require(dirname(__FILE__) . '/freshsales/Client.php');

/**
 * Class Freshsales
 */
class FreshsalesAnalytics
{
    /**
     * @var
     */
    private static $client;

    /**
     * fsalesInitializes the default client to use.
     *
     */
    public static function fsalesInit($properties) {
        self::fsalesAssert(!empty($properties['domain']), "Freshsales::fsalesInit() requires domain");
        self::fsalesAssert(!empty($properties['app_token']), "Freshsales::fsalesInit() requires app token");
        self::$client = new FsalesSmackClient($properties);
    }

    /**
     * @param array $properties
     * @throws Exception
     */
    public static function fsalesIdentify(array $properties) {
        self::fsalesCheckClient();
        self::fsalesAssert(!is_null($properties), "Freshsales::fsalesIdentify() properties should not be null)");
        self::fsalesAssert(!empty($properties['identifier']), "Freshsales::fsalesIdentify() requires identifier)");
        return self::$client->fsalesIdentify($properties);

    }

    /**
     * @param array $properties
     * @return mixed
     * @throws Exception
     */
    public static function fsalesTrackEvent(array $properties){
        self::fsalesCheckClient();
        self::fsalesAssert(!is_null($properties), "Freshsales::fsalesTrackEvent() properties should not be null)");
        self::fsalesAssert(!empty($properties['identifier']), "Freshsales::fsalesTrackEvent() requires identifier)");
        self::fsalesAssert(!empty($properties['name']), "Freshsales::fsalesTrackEvent() expects an event name)");
        return self::$client->fsalesTrackEvent($properties);

    }

    /**
     * @param array $properties
     * @return mixed
     * @throws Exception
     */
    public static function fsalesTrackPageView(array $properties){
        self::fsalesCheckClient();
        self::fsalesAssert(!is_null($properties), "Freshsales::fsalesTrackPageView() properties should not be null)");
        self::fsalesAssert(!empty($properties['identifier']), "Freshsales::fsalesTrackPageView() requires identifier)");
        self::fsalesAssert(!empty($properties['url']), "Freshsales::fsalesTrackPageView expects Page View URL)");
        return self::$client->fsalesTrackPageView($properties);

    }

    /**
     * Check the client.
     *
     * @throws Exception
     */
    private static function fsalesCheckClient(){
        if (null != self::$client) return;
        throw new Exception("Freshsales::fsalesInit() must be called before any other tracking method.");
    }

    /**
     * fsalesAssert `value` or throw.
     *
     * @param array $value
     * @param string $msg
     * @throws Exception
     */
    private static function fsalesAssert($value, $msg){
        if (!$value) throw new Exception($msg);
    }

}
