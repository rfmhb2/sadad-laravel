<?php

namespace Sadad\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RestDriver implements DriverInterface
{
    protected $baseUrl = 'https://sadad.shaparak.ir/vpg/api/v0/';

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
        $result = json_decode($result);
        if ($result->ResCode == 0) {
            return ['Authority' => $result->Token];
        } else {
            return ['error' => $result->Description];
        }
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
        try {
            $client = new Client(['base_uri' => $this->baseUrl]);
            $response = $client->request('POST', $uri, ['json' => $data]);

            $rawBody = $response->getBody()->getContents();
            $body = json_decode($rawBody, true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $rawBody = is_null($response) ? '{"Status":-98,"message":"http connection error"}' : $response->getBody()->getContents();
            $body = json_decode($rawBody, true);
        }
        return $body;
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

        if ($result->ResCode == 0) {
            return [
                'Status' => 'success',
                'RefID' => $result,
            ];
        } else {
            return [
                'Status' => 'error',
                'error' => !empty($result['Status']) ? $result['Status'] : null
            ];
        }
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

    //Create sign data(Tripledes(ECB,PKCS7))


//Send Data
    function CallAPI($url, $data = false)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
