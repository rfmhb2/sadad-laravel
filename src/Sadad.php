<?php

namespace Sadad;

use Sadad\Drivers\DriverInterface;
use Sadad\Drivers\RestDriver;

class Sadad
{
    private $redirectUrl = 'https://sadad.shaparak.ir/VPG/Purchase?Token=%s';
    private $merchantID;
    private $terminalId;
    private $key;
    private $driver;
    private $token;

    public function __construct($merchantID, $terminalId, $key, DriverInterface $driver = null)
    {
        if (is_null($driver)) {
            $driver = new RestDriver();
        }
        $this->merchantID = $merchantID;
        $this->terminalId = $terminalId;
        $this->key = $key;
        $this->driver = $driver;
    }

    /**
     * send request for money to sadad
     * and redirect if there was no error.
     *
     * @param $callbackURL
     * @param $amount
     * @param $dateTime
     * @param $orderId
     * @param null $additionalData
     * @param null $multiplexingData
     * @return array @redirect
     * @internal param $ReturnUrl
     * @internal param string $Amount
     */
    public function request($callbackURL, $amount, $dateTime, $orderId, $additionalData = null, $multiplexingData = null)
    {
        $SignData = $this->encrypt_pkcs7("$this->terminalId;$orderId;$amount", "$this->key");
        $inputs = [
            'TerminalId' => $this->terminalId,
            'MerchantId' => $this->merchantID,
            'Amount' => $amount,
            'SignData' => $SignData,
            'ReturnUrl' => $callbackURL,
            'LocalDateTime' => $dateTime,
            'OrderId' => $orderId
        ];
        if (!is_null($additionalData)) {
            $inputs['AdditionalData'] = $additionalData;
        }

        if (!is_null($multiplexingData)) {
            $inputs['MultiplexingData'] = $multiplexingData;
        }

        $results = $this->driver->request($inputs);

        if (!empty($results['Authority'])) {
            $this->token = $results['Authority'];
        }

        return $results;
    }

    /**
     * verify that the bill is paid or not
     * by checking token.
     *
     * @param $token
     * @return array
     * @internal param $amount
     * @internal param $authority
     */
    public function verify($token)
    {
        $verifyData = array('Token' => $token, 'SignData' => $this->encrypt_pkcs7($token, $this->key));
        return $this->driver->verify($verifyData);
    }

    /**
     *  sign data with key
     * @param $str
     * @param $key
     * @return string
     */
    public function encrypt_pkcs7($str, $key)
    {
        $key = base64_decode($key);
        $ciphertext = OpenSSL_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);
        return base64_encode($ciphertext);
    }

    public function redirect()
    {
        header('Location: ' . $this->redirectUrl());
        die;
    }

    /**
     * @return string
     */
    public function redirectUrl()
    {
        return sprintf($this->redirectUrl, $this->token);
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
