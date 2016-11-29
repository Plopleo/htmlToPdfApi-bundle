<?php

namespace HtmlToPdfBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

class PdfController extends AbstractController
{
    /**
     * @Route("/wrapper/{call}", name="htmltopdf_wrapper")
     */
    public function wrapperAction(Request $request, $call)
    {
        $html = $request->get('html', false);
        $paramsAll = explode(';', $request->get('params'));
        $params = [];

        foreach($paramsAll as $i => $param){
            $temp = explode(':', $param);
            $params[$temp[0]] = $temp[1];
        }

        //$url = $this->generateUrl($route, $params, UrlGenerator::ABSOLUTE_URL);
        $response = $this->forward($call, $params);

        $htmlContent = $response->getContent();

        if(!$html){
            return $this->renderPdfFromHtml($htmlContent, strtolower(str_replace(':', '-', $call)).'_'.date('Y-m-d_His').'.pdf');
        }

        $response = new Response();
        $response->setContent($htmlContent);

        return $response;

    }
    
    /**
     * @Route("/test/{message}", name="htmltopdf_test")
     */
    public function testAction(Request $request, $message)
    {
        return new Response('<html><body><div class="page">'.$message.'</div></body></html>');
    }
}