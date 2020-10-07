<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Zend\Barcode\Barcode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\Response;

class Code extends MY_Controller
{

    /************/
    public function __construct()
    {
        parent::__construct();
    }
    /************/
    public function qr()
    {
        if ($this->_is_login) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $qrCode = new QrCode($arg[0]);
            }
        }else{
            $qrCode = new QrCode('You are not logged in yet. Please Login! '.base.'/login');
        }
        // $qrCode->setLogoPath(BASE.'/files/images/logo.png');
            // ->setWriterByName('png')
            // ->setMargin(10)
            // ->setEncoding('UTF-8')
            // ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            // ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            // ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
            // ->setLabel('Scan the code', 16, __DIR__.'/../assets/noto_sans.otf', LabelAlignment::CENTER)
            // ->setLogoPath(__DIR__.'/../assets/symfony.png')
            // ->setLogoWidth(150)
            // ->setValidateResult(false)
            // ;


        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }
    /************/
    public function bar()
    {
        // $this->_allowed_to(array(role_admin));
        // if ($this->_is_login) {
            $option['font']         = './files/fonts/Consolas_Bold.ttf';
            $option['fontSize']     = '7';
            $option['stretchText']  = true;
            $option['withChecksum'] = false;
            $arg                    = func_get_args();
            $option['text']         = isset($arg[0]) ? $arg[0] : '';
            $option['factor']       = isset($arg[1]) ? $arg[1] : '2';
            $option['barHeight']    = isset($arg[2]) ? $arg[2] : '32'; 
            // $option['withBorder']  = ;

            // $file = Barcode::draw('code128', 'image', $option); // gak kebaca scanner
            // $file = Barcode::draw('code39', 'image', $option);
            $file = Barcode::draw('code25interleaved', 'image', $option);
            // $file = Barcode::draw('code25', 'image', $option); //xxx kekecilan
            // $file = Barcode::draw('upc-a', 'image', $option); //xxx kekecilan
            // $file = Barcode::draw('leitcode', 'image', $option); // leading zero
            // array('text' => '1234','barHeight'=>20, 'factor'=>3) );
            header('Content-Type: image/png');
            echo imagepng($file);

            // ob_start(function ($c) {return 'data:image/png;base64,' . base64_encode($c);});
            // imagepng($file);
            // ob_end_flush();

        // }
    }
    /************/
}

/* End of file Code.php */
/* Location: ./application/controllers/Code.php */
