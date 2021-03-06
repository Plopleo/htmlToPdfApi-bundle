<?php

namespace HtmlToPdfBundle\Api;

use Monolog\Logger;

class Client
{
    /** @var \GuzzleHttp\Client */
    protected $client = null;
    protected $logger = null;

    protected $default_margin_top = '10mm';
    protected $default_margin_bottom = '10mm';

    protected $default_margin_left = '10mm';
    protected $default_margin_right = '10mm';

    /**
     * Client constructor.
     * @param $client
     * @param Logger $logger
     */
    public function __construct($client, $logger) {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param string $default_margin_top
     */
    public function setDefaultMarginTop($default_margin_top)
    {
        $this->default_margin_top = $default_margin_top;
    }

    /**
     * @param string $default_margin_bottom
     */
    public function setDefaultMarginBottom($default_margin_bottom)
    {
        $this->default_margin_bottom = $default_margin_bottom;
    }

    public function getPdfContent($html, $titre = '', $options = array())
    {
        $response = $this->client->post(
            'htmltopdf',
            array(
                'form_params' => [
                    'html_content' => $html,
                    'footer-margin-bottom' => isset($options['footer-margin-bottom'])?$options['footer-margin-bottom']:$this->default_margin_bottom,
                    'header-margin-top' => isset($options['header-margin-top'])?$options['header-margin-top']:$this->default_margin_top,
                    'margin-left' => isset($options['margin-left'])?$options['margin-left']:$this->default_margin_left,
                    'margin-right' => isset($options['margin-right'])?$options['margin-right']:$this->default_margin_right,
                ]
            )
        );

        $this->logger->addInfo('PDF generation: '.$response->getReasonPhrase());
        if ($response->getReasonPhrase() == 'OK') {
            $json = json_decode($response->getBody()->getContents(), true);
            return utf8_decode($json['pdf_content']);
        }
    }
}
