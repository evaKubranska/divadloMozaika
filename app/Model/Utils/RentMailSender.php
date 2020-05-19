<?php


namespace App\Model;


use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class RentMailSender {

    public static function sendAcceptationMail(Customer $customer): void {
        $body = "Dobrý deň pán/pani $customer->lastName, \n žiadosť o rezerváciu sály" .
            " bola prijatá. V nasledujúcich dnňoch Vás budeme kontaktovať a dohodneme podrobnosti ".
            "\n Prajem pekný deň. \n Vaše Divadlo.";
        $mail = new Message();
        $mail->setFrom('divadlo@divadlo.mydomain');
        $mail->addTo($customer->email);
        $mail->setSubject('potvrdenie rezervácie');
        $mail->setBody($body);
        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }

    /**
     * @param Customer $customer
     */
    public static function sendRejectionMail(Customer $customer): void {
        $body = "Dobrý deň pán/pani $customer->lastName, \n vaša žiadosť o rezerváciu sály " .
            ' bola zamietnutá. V prípade akýchkoľvek otázok nás môžete kontaktovať,  ' .
            " \n Prajem pekný deň. \n Vaše Divadlo.";
        $mail = new Message();
        $mail->setFrom('divadlo@divadlo.mydomain');
        $mail->addTo($customer->email);
        $mail->setSubject('zamietnutie rezervácie');
        $mail->setBody($body);
        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }
}