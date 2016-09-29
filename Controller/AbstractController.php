<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 29/09/16
 * Time: 11:20
 */

namespace HtmlToPdfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends Controller
{

    public function renderHtml($template, $params)
    {
        $htmlContent = $this->render($template, $params)->getContent();
        $response = new Response();
        $response->setContent($htmlContent);
        return $response;
    }

    public function renderPdf($template, $params, $titre_pdf = 'default')
    {
        $htmlContent = $this->render($template, $params)->getContent();
        return $this->renderPdfFromHtml($htmlContent, $titre_pdf);
    }

    public function renderPdfFromHtml($htmlContent, $titre_pdf = 'default')
    {
        $pdfContent = $this->get('htmltopdf.client')->getPdfContent($htmlContent);

        $response = new Response();
        $response->setContent($pdfContent);
        $response->headers->set('Content-type', 'application/pdf');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $titre_pdf.'_'.date('Y-m-d_His').'.pdf'));
        return $response;
    }

    public function returnResponse($html, $template, $params, $titre_pdf = 'default')
    {
        $response = $html ? $this->renderHtml($template, $params) : $this->renderPdf($template, $params, $titre_pdf);

        return $response;
    }

}