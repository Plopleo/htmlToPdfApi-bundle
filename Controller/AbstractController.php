<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 29/09/16
 * Time: 11:20
 */

namespace HtmlToPdfBundle\Controller;

use Spatie\PdfToImage\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
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

    public function renderPdf($template, $params, $titre_pdf = 'default', $options = [])
    {
        $htmlContent = $this->render($template, $params)->getContent();
        $htmlContent = str_replace(['€', '’'], ['EUR', '\''], $htmlContent); //problématique sur certains caractères
        return $this->renderPdfFromHtml($htmlContent, $titre_pdf, $options);
    }

    public function renderPdfFromHtml($htmlContent, $titre_pdf = 'default', $options = [])
    {
        $pdfContent = $this->get('htmltopdf.client')->getPdfContent($htmlContent, $titre_pdf, $options);

        if(isset($options['protected']) && $options['protected']){
            $filesToRemove = [];

            $webPath = 'tmp/pdf/'.uniqid().'.pdf';
            $path = $this->getParameter('kernel.root_dir').'/../web/'.$webPath;
            $filesToRemove[] = $path;
            if(!is_dir(dirname($path))){
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $pdfContent);

            $htmlImage = '
<html>
    <head></head>
    <body>
';
            $imagick = new Pdf($path);
            for($i = 1; $i <= $imagick->getNumberOfPages(); $i++){
                $pathImage = $path.'.'.$i.'.jpg';
                $imagick
                    ->setPage($i)
                    ->setOutputFormat('jpeg')
                    ->setResolution(450, 636) //compromis entre la taille du fichier et la taille de l image
                    ->saveImage($pathImage)
                ;
                $filesToRemove[] = $pathImage;

                $htmlImage .= '
        <div class="page"><img src="'.$this->get('request_stack')->getMasterRequest()->getSchemeAndHttpHost().'/'.$webPath.'.'.$i.'.jpg" style="width: 99%"></div>
';
            }
            $htmlImage .= '
    </body>
</html>
';
            $pdfContent = $this->get('htmltopdf.client')->getPdfContent(
                $htmlImage,
                $titre_pdf,
                [
                    'footer-margin-bottom' => 0,
                    'header-margin-top' => 0,
                    'margin-left' => 0,
                    'margin-right' => 0
                ]
            );

            foreach($filesToRemove as $file){
                unlink($file);
            }
        }

        $response = new Response();
        $response->setContent($pdfContent);
        $response->headers->set('Content-type', 'application/pdf');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $titre_pdf.'_'.date('Y-m-d_His').'.pdf'));
        return $response;
    }

    public function returnResponse($html, $template, $params, $titre_pdf = 'default', $options = [])
    {
        $response = $html ? $this->renderHtml($template, $params) : $this->renderPdf($template, $params, $titre_pdf, $options);

        return $response;
    }

}