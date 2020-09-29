<?php
namespace houyimin\feishu;

use GuzzleHttp\Client;
use yii\base\Component;
use yii\base\InvalidConfigException;
use GuzzleHttp\Exception\RequestException;

class Robot extends Component
{
    /**
     * @var string
     */
    public $accessToken;

    /**
     * @var string
     */
    public $apiUrl_v1 = 'https://open.feishu.cn/open-apis/bot/hook/';
    public $apiUrl = 'https://open.feishu.cn/open-apis/bot/v2/hook/';

    /**
     * @var string
     */
    public $apiVersion = 'v2';

    /**
     * @var array
     */
    public $guzzleOptions = [];

    /**
     * @var array
     */
    public $msgTypeList = ['text','post'];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->accessToken === null) {
            throw new InvalidConfigException('The "accessToken" property must be set.');
        }
        if ($this->apiVersion == 'v1'){
            $this->apiUrl = $this->apiUrl_v1;
        }
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @param array $options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 发送文本消息
     * @param string $content
     * @return mixed
     */
    public function sendTextMsg($content)
    {
        $query = [
            'msg_type' => 'text',
            'content' => [
                'text' => $content,
            ]
        ];

        return $this->sendMsg($query);
    }

    /**
     * 发送富文本
     * @param $content
     * @return mixed
     */
    public function sendPostMsg($content)
    {
        $query = [
            'msg_type' => 'post',
            'content' => [
                'post' => $content
            ]
        ];
        return $this->sendMsg($query);
    }


    /**
     * @param string $type
     * @param array $msgData
     * @return mixed
     */
    public function sendMsg(array $msgData=[])
    {
        try {
            $response = $this->getHttpClient()->post($this->apiUrl.$this->accessToken, [
                \GuzzleHttp\RequestOptions::JSON => $msgData,
                'headers' => [
                    'Content-Type'=> 'application/json;charset=utf-8'
                ],

            ])->getBody()->getContents();
             return $response;
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
