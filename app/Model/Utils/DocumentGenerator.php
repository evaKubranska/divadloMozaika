<?php


namespace App\Model;


use Latte\Engine;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Random;

class DocumentGenerator {
    private Mpdf $mpdf;
    /**
     * @var Engine
     */
    private Engine $latte;
    private string $templates;
    private string $directory;
    private string $cssDirectory;

    /**
     * DocumentGenerator constructor.
     * @throws MpdfException
     */
    public function __construct() {
        $this->mpdf = new Mpdf();
        $this->latte = new Engine();
        $this->latte->setTempDirectory(APP_DIR.'temp');
        $this->templates = str_replace('/', DIRECTORY_SEPARATOR, APP_DIR.'app/Presenters/templates/emails/');
        $this->directory = str_replace('/', DIRECTORY_SEPARATOR, APP_DIR.'www/customDocuments/');
        $this->cssDirectory = str_replace('/', DIRECTORY_SEPARATOR, APP_DIR.'www/styles/');

    }

    /**
     * @param BookedTicket $bookedTicket
     * @return String
     * @throws MpdfException
     */
    public function generateBuyTicketDocumentation(BookedTicket $bookedTicket):String {
        $this->mpdf->SetTitle('Vstupenka');
        $html = $this->latte->renderToString($this->templates.'ticketBuy.latte', ['bookedTicket' =>$bookedTicket]);
        $stylesheet = file_get_contents($this->cssDirectory.'ticketBuy.css');
        $this->mpdf->WriteHTML($stylesheet,HTMLParserMode::HEADER_CSS);
        $this->mpdf->WriteHTML($html, HTMLParserMode::HTML_BODY);
        FileSystem::createDir($this->directory);
        $fileName = Random::generate(10).'.pdf';
        $this->mpdf->Output($this->directory.$fileName , Destination::FILE);
        return $fileName;
    }

    /**
     * @param BookedTicket $bookedTicket
     * @return String
     * @throws MpdfException
     */
    public function generateCancelTicketDocumentation(BookedTicket $bookedTicket):String {
        $this->mpdf->SetTitle('Storno listka');
        $cancelDate = DateTime::from(0);
        $html = $this->latte->renderToString($this->templates.'ticketCancel.latte',['bookedTicket' =>$bookedTicket, 'cancelDate' =>$cancelDate]);
        $stylesheet = file_get_contents($this->cssDirectory.'ticketCancel.css');
        $this->mpdf->WriteHTML($stylesheet,HTMLParserMode::HEADER_CSS);
        $this->mpdf->WriteHTML($html, HTMLParserMode::HTML_BODY);
        FileSystem::createDir($this->directory);
        $fileName =  Random::generate(10).'.pdf';
        $this->mpdf->Output($this->directory.$fileName, Destination::FILE);
        return $fileName;
    }
}
