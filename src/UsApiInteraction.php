<?php
/**
 * Created by PhpStorm.
 * User: Denis Bondar
 * Date: 17.09.2017
 * Time: 20:40
 */

namespace denisbondar\userside\api;


use linslin\yii2\curl\Curl;
use yii\helpers\ArrayHelper;

class UsApiInteraction
{
    // ***************************************************
    const URL = 'http://billing.gorodok.zp.ua/api.php';

    private $baseParams = [
        'key' => 'xyRXP5PBape7',
    ];
    // ***************************************************

    private $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * Prepare USERSIDE API Request
     *
     * @param $params
     * @return UsApiInteraction
     */
    public function prepare($params)
    {
        $params = ArrayHelper::merge($this->baseParams, $params);

        $this->curl->setGetParams($params);

        return $this;
    }

    /**
     * Make GET request to USERSIDE API
     *
     * @return mixed Response
     */
    public function get()
    {
        $response = json_decode($this->curl->get(self::URL));
        $this->checkResponse($response);

        return $response;
    }

    /**
     * Check for errors in API Request & Response
     *
     * @param $response
     * @throws \ErrorException
     */
    private function checkResponse($response)
    {
        if ($this->curl->errorCode !== null) {
            throw new \ErrorException(
                sprintf('USERSIDE API Interaction error: %s', $this->curl->errorText),
                $this->curl->responseCode
            );
        }

        if ($response->Result !== 'OK') {
            throw new \ErrorException(
                sprintf('API Response Error: %s', $response->ErrorText));
        }
    }
}
