<?php
/**
 * @link      https://github.com/denisbondar/yii2-userside-api
 * @package   yii2-userside-api
 * @author    Denis Bondar <bondar.den@gmail.com>
 * @license   MIT License - view the LICENSE file that was distributed with this source code.
 * @date      17.09.2017
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
     * Make GET request to USERSIDE API
     *
     * @param $params
     * @return mixed Response
     */
    public function get($params)
    {
        $params = ArrayHelper::merge($this->baseParams, $params);
        $this->curl->setGetParams($params);
        $response = json_decode($this->curl->get(self::URL));
        $this->checkResponse($response);

        return $response;
    }

    public function post($params)
    {
        $params = ArrayHelper::merge($this->baseParams, $params);
        $this->curl->setPostParams($params);
        $response = json_decode($this->curl->post(self::URL));
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

    /**
     * Включает режим обратной сортировки выборки
     *
     * @return UsApiInteraction
     */
    public function sortDesc()
    {
        $this->curl->setGetParams(['sort_desc' => 1]);

        return $this;
    }
}
