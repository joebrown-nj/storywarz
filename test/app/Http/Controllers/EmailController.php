<?php

namespace App\Http\Controllers;

use Brevo\Brevo;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;

// use Brevo\TransactionalSms\Requests\SendTransacSms;
// use Brevo\TransactionalSms\Types\SendTransacSmsRequestSender;
// use Brevo\TransactionalSms\Types\SendTransacSmsRequestToItem;
use Brevo\Types\SendTransacSms;

class EmailController extends Controller
{
    public function sendEmail($toEmail, $toName, $subject, $content)
    {
        $client = new Brevo(
            apiKey: env('BREVO_API_KEY'),
        );

        $sendSmtpEmail = new SendTransacEmailRequest(
            sender: new SendTransacEmailRequestSender([
                'email' => 'joebro84@yahoo.com',
                'name' => 'Story Warz',
            ]),
            subject: $subject,
            htmlContent: $content,
            to: [
                new SendTransacEmailRequestToItem([
                    'email' => $toEmail,
                    'name' => $toName,
                ]),
            ],
        );

        try {
            $response = $client->transactionalEmails->sendTransacEmail($sendSmtpEmail);
            return $response;
        } catch (Exception $e) {
            echo 'Error sending transactional email: ' . $e->getMessage();
            return;
        }
    }

    public function sendSms($toPhone, $content)
    {
        $client = new Brevo(
            apiKey: env('BREVO_API_KEY'),
        );

        // $smsMessage = new SendTransacSms([
        //     'recipient' => '7329254044',
        //     'sender' => 'StoryWarz',
        //     'content' => $content,
        //     'type' => 'marketing',
        // ]);

        // try {
        //     $res = $client->transactionalSms->sendAsyncTransactionalSms($smsMessage);
        //     print_r($res);
        // } catch (Exception $e) {
        //     echo 'Error setting SMS content: ' . $e->getMessage();
        //     return;
        // }

        $sendSmsMessage = new SendTransacSms([
            'recipient' => $toPhone,
            'sender' => 'StoryWarz',
            'content' => $content,
        ]);

        try {
            $response = $client->transactionalSms->sendAsyncTransactionalSms($sendSmsMessage);
            print_r($response);
        } catch (Exception $e) {
            echo 'Error sending transactional SMS: ' . $e->getMessage();
        }
    }
}


// $client = new Brevo(
//     apiKey: env('BREVO_API_KEY'),
// );

// echo env('BREVO_API_KEY');
// echo '<br>';

// try {
//     $response = $client->transactionalEmails->sendTransacEmail(
//         new SendTransacEmailRequest([
//             'htmlContent' => '<html><head></head><body><p>Hello,</p>This is my first transactional email sent from Brevo.</p></body></html>',
//             'sender' => new SendTransacEmailRequestSender([
//                 'email' => 'joebro84@yahoo.com',
//                 'name' => 'Story Warz',
//             ]),
//             'subject' => 'Test',
//             'to' => [
//                 new SendTransacEmailRequestToItem([
//                     'email' => 'joebro84@yahoo.com',
//                     'name' => 'John Doe',
//                 ]),
//             ],
//         ]),
//     );
//     print_r($response);
// } catch (Exception $e) {
//     echo 'Error sending transactional email: ' . $e->getMessage();
// }