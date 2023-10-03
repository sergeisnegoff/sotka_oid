<?php

namespace App\Exports;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

define('K_PATH_FONTS', database_path('tcpdf/fonts/'));
define('K_PATH_IMAGES', database_path('tcpdf/images/'));

abstract class TcpdfAbstract extends \TCPDF {

    const TYPE_DOWNLOAD = 'download';
    const TYPE_SHOW = 'show';
    const TYPE_PRINT = 'print';
    const TYPE_SEND = 'send';

    private $share_data = [];
    private $url = null;
    protected $pdf_ns = '';
    protected $use_header = false;
    protected $use_footer = false;

    public function Header(){
        if($this->use_header){
            $this->renderView('pdf.'.$this->pdf_ns.'.header');
        }
    }
    public function Footer(){
        if($this->use_footer) {
            $this->renderView('pdf.' . $this->pdf_ns . '.footer');
        }
    }

    public function renderView($view, $data = [], $pdf_data = []){
        $d = array_merge([
            'ln' => true,
            'fill' => false,
            'reseth' => false,
            'cell' => false,
            'align' => ''
        ], $pdf_data);
        $this->writeHTML(View::make($view, $this->share_data, $data)->render(), $d['ln'], $d['fill'], $d['reseth'], $d['cell'], $d['align']);
    }

    public function getUrl(){
        return $this->url;
    }

    /**
     * @return array
     */
    abstract function pdfShare();
    /**
     * @return void
     */
    abstract function pdfSettings();
    /**
     * @return array
     */
    abstract function pdfConstructor();

    /**
     * @return string
     */
    abstract function getSendFilename();

    protected function getTitle(){
        return request()->getHost().'.pdf';
    }

    public function __construct($output = self::TYPE_SHOW){

        $this->share_data = $this->pdfShare();

        parent::__construct(...$this->pdfConstructor());

        $this->SetTitle($this->getTitle());
        $this->SetFont('montserrat', '', 8);
        $this->SetHeaderFont(['montserrat', '', PDF_FONT_SIZE_MAIN]);
        $this->SetFooterFont(['montserrat', '', PDF_FONT_SIZE_MAIN]);
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetAutoPageBreak(true, 0);
        $this->SetImageScale(PDF_IMAGE_SCALE_RATIO);

        $this->pdfSettings();

        $this->AddPage();

        $this->renderView('pdf.'.$this->pdf_ns.'.body');

        $this->save($output);
    }

    protected function save($output){
        switch($output){
            case static::TYPE_SEND;
                $main_part = '/'.$this->pdf_ns.'/'.sha1(date('Y_m_d')).'/'.$this->getSendFilename().'.pdf';
                $path = storage_path('app/public'.$main_part);
                File::makeDirectory(dirname($path), 0755, true, true);
                $this->Output($path, 'F');
                $this->url = asset('/storage'.$main_part);
                break;
            case static::TYPE_PRINT;
                $this->IncludeJS('print(true);');
                $this->Output(\request()->getHost().'_'.date('_Y_m_d').'.pdf', 'I');
                exit;
                break;
            case static::TYPE_SHOW;
                $this->Output(\request()->getHost().'_'.date('_Y_m_d').'.pdf', 'I');
                exit;
                break;
            case static::TYPE_DOWNLOAD;
                $this->Output(\request()->getHost().'_'.date('_Y_m_d').'.pdf', 'D');
                exit;
                break;
        }
    }
}
