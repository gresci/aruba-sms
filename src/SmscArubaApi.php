<?php

namespace NotificationChannels\SmscAruba;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\SmscAruba\Exceptions\CouldNotSendNotification;

class SmscArubaApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl = 'https://admin.sms.aruba.it/sms/send.php';

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $login;

    /** @var string */
    protected $secret;

    /** @var string */
    protected $sender;

    public function __construct($login, $secret, $sender)
    {
        $this->login = $login;
        $this->secret = $secret;
        $this->sender = $sender;

        $this->httpClient = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * @param  array  $params
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        $base = [
            'charset' => 'utf-8',
            'login'   => $this->login,
            'psw'     => $this->secret,
            'sender'  => $this->sender,
            'fmt'     => self::FORMAT_JSON,
        ];

        $params = array_merge($base, $params);

        try {
            $response = $this->httpClient->post($this->apiUrl, ['form_params' => $params]);

            $response = json_decode((string) $response->getBody(), true);

            if (isset($response['error'])) {
                throw new DomainException($response['error'], $response['error_code']);
            }

            return $response;
        } catch (DomainException $exception) {
            throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }
}
