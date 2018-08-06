<?php

namespace NotificationChannels\SmscAruba;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\SmscAruba\Exceptions\CouldNotSendNotification;

class SmscArubaApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl = 'http://admin.sms.aruba.it/sms/batch.php?';

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $login;

    /** @var string */
    protected $secret;

    /** @var string */
    protected $sender;

    /** @var string */
    protected $quality;

    public function __construct($login, $secret, $sender, $quality='h')
    {
        $this->login = $login;
        $this->secret = $secret;
        $this->sender = $sender;
        $this->quality = $quality;

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
            'user'   => $this->login,
            'pass'     => $this->secret,
            'sender'  => $this->sender,
            'qty'     => $this->quality,
        ];

        $params = array_merge($base, $params);

        try {
            $response = $this->httpClient->request('GET', $this->apiUrl, [
                'query' => $params
            ]);

            $response = (string) $response->getBody();

            if(substr($response, 0, 2) !== "OK"){
                throw new DomainException($response, $response);
            }

            return $response;
        } catch (DomainException $exception) {
            throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }
}
