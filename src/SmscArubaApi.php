<?php

namespace NotificationChannels\SmscAruba;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\SmscAruba\Exceptions\CouldNotSendNotification;

class SmscArubaApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl = 'http://admin.sms.aruba.it/sms/send.php';

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

    public function __construct($login, $secret, $sender, $quality='l')
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
            //user=$SMSuser&pass=$SMSpass&rcpt=$rcpt&data=$messaggio&sender=$mittente&qty=$quality
//            'charset' => 'utf-8',
            'user'   => $this->login,
            'pass'     => $this->secret,
            'sender'  => $this->sender,
            'qty'     => $this->quality,
        ];

        $params = array_merge($base, $params);

        try {
//            $response = $this->httpClient->post($this->apiUrl, ['form_params' => $params]);
            $response = $this->httpClient->get($this->apiUrl, $params);

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
