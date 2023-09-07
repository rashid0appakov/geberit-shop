<?php

/**
* Simple response class
*
* @author  nkorostelev@gmail.com
*/

namespace WebServiceCrm\ApiClient\Response;

class Response
{
    private $statusCode;

    private $responseBody;

    public function __construct($statusCode, $responseBody = null)
    {
        $this->statusCode = $statusCode;

        if (!empty($responseBody)) {
            $response = json_decode($responseBody, true);

            if ($response == false) {
                throw new \Exception("Filed to get correct json response");
            }
        }

        $this->responseBody = $response;
    }

    public function isSuccess()
    {
        return $this->statusCode < 400;
    }

    /**
     * Return response
     *
     * @return array Array data
     */
    public function getResponse()
    {
        return $this->responseBody;
    }

    /**
     * Return status code of response
     *
     * @return integer Response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function __call($property, $arguments)
    {
        if (isset($this->responseBody[$property])) {
            return $this->responseBody[$property];
        }

        return false;
    }
}
?>