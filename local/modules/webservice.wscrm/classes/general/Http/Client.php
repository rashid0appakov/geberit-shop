<?php

namespace WebServiceCrm\ApiClient\Http;

use WebServiceCrm\ApiClient\Exception\CurlException;
use WebServiceCrm\ApiClient\Response\Response;

class Client
{
    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';

    /**
     * Conig data
     * @var array
     */
    protected $config = array();

    /**
     * Defatult parameters
     * @var array
     */
    protected $defaultParameters = array();

    public function __construct(array $parameters, array $config)
    {
        if ($config['base_uri']) {
            $config['base_uri'] = $this->checkUri($config['base_uri']);
        }

        $this->mergeDefaultParameters($parameters);
        $this->mergeDefaultConfig($config);
    }

    /**
     * Merge data with default parameters
     * @param  array  $parameters
     * @return void
     */
    public function mergeDefaultParameters(array $parameters)
    {
        $defaultParameters = [];
        $this->defaultParameters = array_merge($defaultParameters, $parameters);
    }

    public function mergeDefaultConfig(array $config)
    {
        $defaultConfig = ['debug' => false];
        $this->config = array_merge($defaultConfig, $config);
    }

    public function request($url, $method = 'GET', array $data)
    {
        if (! in_array($method, array(self::METHOD_POST, self::METHOD_GET))) {
            throw new \InvalidArgumentException(
                "{$method} is not allowed, please use default methods: " . impode(', ', $defaultMethods), 1
            );
        }

        $data = array_merge($this->defaultParameters, $data);

        if ($this->config['base_uri']) {
            $url = $this->config['base_uri'] . $url;
        }

        if ($method === self::METHOD_GET && count($data)) {
            $url .= '?' . http_build_query($data, '', '&');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        if ($method === self::METHOD_POST) {
            $jsonString = json_encode($data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonString)
            ));
        }

        if ($this->config['debug'] === true) {
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $resultBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        // Close curl connection
        curl_close($ch);

        if ($errno) {
            throw new CurlException($error, $errno);
        }

        return new Response($statusCode, $resultBody);
    }

    /**
     * Check uri
     *
     * @param  string|null $value Uri string
     * @return string Valid clear uri
     */
    private function checkUri($value = null)
    {
        if ($value == null) {
            throw new \InvalidArgumentException("checkUri must be required");
        }

        if (($parsedUri = parse_url($value)) == false) {
            throw new \InvalidArgumentException("Failed to parse url");
        }
        else {
            return $value;
        }

        throw new \InvalidArgumentException('URI must be a string');
    }
}

?>