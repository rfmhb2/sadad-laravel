<?php

namespace Sadad\Drivers;


class RestDriver implements DriverInterface
{
    protected $baseUrl = 'https://sadad.shaparak.ir/vpg/api/v0/';
    protected $baseUrl2 = 'https://sadad.shaparak.ir/vpg/api/v0/';
    /**
     * request driver.
     *
     * @param $inputs
     *
     * @return array
     */
    public function request($inputs)
    {
        $result = $this->restCall('Request/PaymentRequest', $inputs);
        $result = json_decode($result, false);
        if ($result->ResCode == 0) {
            return ['Authority' => $result->Token];
        } else {
            return ['error' => $result->Description];
        }
    }

    /**
     * verify driver.
     *
     * @param $inputs
     *
     * @return array
     */
    public function verify($inputs)
    {
        $result = $this->restCall('Advice/Verify', $inputs);
        $result = json_decode($result, false);
        if ($result->ResCode == 0) {
            return [
                'Status' => 'success',
                'RefID' => $result,
            ];
        }
        return [
            'Status' => 'error',
            'error' => !empty($result['Status']) ? $result['Status'] : null
        ];
    }

    /**
     * @param mixed $baseUrl
     *
     * @return void
     */
    public function setAddress($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * request rest and return the response.
     *
     * @param $uri
     * @param $data
     *
     * @return mixed
     */
    private function restCall($uri, $data)
    {
        $url = $this->baseUrl . $uri;
        $data = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($data)]);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }


}
