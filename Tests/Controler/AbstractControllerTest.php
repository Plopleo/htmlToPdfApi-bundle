<?php
namespace Tests\HtmlToPdfBundle\Controller;

use HtmlToPdfBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractControllerTest extends WebTestCase
{

    public function testRenderPdfFromHtml()
    {
        static::bootKernel();
        /** @var AbstractController $controller */
        $controller = static::$kernel->getContainer()->get('htmltopdf.controller.service');
        $controller->setContainer(static::$kernel->getContainer());

        $html = "<html><head></head><body>test</body>";
        $response = $controller->renderPdfFromHtml($html);
        $pdf = $response->getContent();

        $this->assertEquals(3362, strlen($pdf));
    }
}