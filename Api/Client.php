<?php

namespace HtmlToPdfBundle\Api;

class Client
{
    /** @var \GuzzleHttp\Client */
    protected $client = null;

    protected $default_margin_top = '10mm';
    protected $default_margin_bottom = '10mm';

    public function __construct($client) {
        $this->client = $client;
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
                ]
            )
        );

        if ($response->getReasonPhrase() == 'OK') {
            $json = json_decode($response->getBody()->getContents(), true);
            return utf8_decode($json['pdf_content']);
        }
    }
}
