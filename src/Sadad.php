<?php

namespace Sadad;

use Sadad\Drivers\DriverInterface;
use Sadad\Drivers\RestDriver;

class Sadad
{
    private $redirectUrl = 'https://sadad.shaparak.ir/VPG/Purchase?Token=%u';
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
     * @param $ReturnUrl
     * @param string $Amount
     * @param $dateTime
     * @return array @redirect
     */
    public function request($ReturnUrl, $Amount, $dateTime)
    {
        $inputs = [
            'TerminalId' => $this->terminalId,
            'MerchantId' => $this->merchantID,
            'Amount' => $Amount,
            'SignData' => $SignData,
            'ReturnUrl' => $ReturnUrl,
            'LocalDateTime' => $dateTime,
            'OrderId' => $OrderId
        ];
        $results = $this->driver->request($inputs);
        if (empty($results['Authority'])) {
            $results['Authority'] = null;
        }
        if ($results->ResCode == 0) {
            $this->token = $results->Token;
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
        $str_data = json_encode($verifyData);
        return $this->driver->verify($str_data);
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
        $block = mcrypt_get_block_size("tripledes", "ecb");
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $ciphertext = mcrypt_encrypt("tripledes", $key, $str, "ecb");
        return base64_encode($ciphertext);
    }

    public function redirect()
    {
        header('Location: ' . sprintf($this->redirectUrl, $this->Authority));
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
