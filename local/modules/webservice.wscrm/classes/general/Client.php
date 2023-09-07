<?php

/**
 * Простой API клиент для работы с WebServiceCrm
 *
 * @author Nikita Korostelev
 * @version  0.0.1
 */
namespace WebServiceCrm\ApiClient;

use WebServiceCrm\ApiClient\Http\Client as HttpClient;

class Client
{
    /**
     * @var object WebServiceCrm\ApiClient\Http\Client
     */
    protected $httpClient;

    /**
     * Site short key
     * @var [type]
     */
    protected $site;

    public function __construct($api_url, $api_key, $site = null)
    {
        $this->site = $site;
        $this->httpClient = new HttpClient(
            array('key' => $api_key),
            array('base_uri' => $api_url)
        );
    }

    /**
     * Send order to WebService CRM
     *
     * @param  array $order order data
     * @return void
     */
    public function createOrder($order)
    {
        return $this->httpClient->request(
            '/order',
            HttpClient::METHOD_POST,
            $this->fillSite($this->site, array('order' => $order))
        );
    }

    /**
     * Fill site code to data
     *
     * @param mixed|string $site
     * @param  array  $data
     * @return array
     */
    public function fillSite($site, array $data)
    {
        if ($site) {
            $data['site'] = $site;
        } elseif ($this->site) {
            $data['site'] = $this->site;
        }

        return $data;
    }
}

?>