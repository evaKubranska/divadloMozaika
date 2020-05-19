<?php


namespace App\Model;


use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class TicketMailSender {

    public static function sendReservationTicketMail(Customer $customer): void {
        $body = "Dobrý deň pán/pani $customer->lastName, \n vaša registrácia" .
            ' prebehla úspešne. Lístky si môžete zakúpiť a vyzdvihnúť na našej ' .
            "pobočke. \n Prajem pekný deň. Divadlo.";
        $mail = new Message();
        $mail->setFrom('divadlo@divadlo.mydomain');
        $mail->addTo($customer->email);
        $mail->setSubject('potvrdenie rezervácie');
        $mail->setBody($body);
        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }

    public  static function sendCancelBuyTicketMail (BookedTicket $bookedTicket): void {
        $custo = $bookedTicket->customer;
        $body = "Dobrý deň pán/pani $custo->lastName \n predstavenie na ktoré ste si zákúpili lístky bolo zrušené." .
            "Na našej pobočke po predložení tohto emailu vám budú vrátené peniaze. Číslo vašeho lístku je : $bookedTicket->idBookedTicket . Ospravedlňujeme sa za spôsobené nepríjemnosti.  " .
            " \n Prajem pekný deň. \n Vaše Divadlo.";
        $mail = new Message();
        $mail->setFrom('divadlo@divadlo.mydomain');
        $mail->addTo($custo->email);
        $mail->setSubject('zrušenie predstavenia');
        $mail->setBody($body);
        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }
    public  static function sendCancelReservationMail (BookedTicket $bookedTicket): void {
        $custo = $bookedTicket->customer;
        $body = "Dobrý deň pán/pani $custo->lastName, \n predstavenie na ktoré ste si zákúpili lístky bolo zrušené." .
            "Ospravedlňujeme sa za spôsobené nepríjemnosti.  " .
            " \n Prajem pekný deň. \n Vaše Divadlo.";
        $mail = new Message();
        $mail->setFrom('divadlo@divadlo.mydomain');
        $mail->addTo($custo->email);
        $mail->setSubject('zrušenie predstavenia');
        $mail->setBody($body);
        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }
}