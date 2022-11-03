<?php

namespace App\Class;

use Mailjet\Client;
use Mailjet\Resources;

class Mail {

    private $apiKey = "60d1bde9ef02072676045dacdaa61567" ;
    private $apiKeySecret = "10e72f7734404a931235e0796f04cce9" ;

    public function sendEmail($to_email, $to_name, $subject, $title, $content) {
        $mj = new Client($this->apiKey, $this->apiKeySecret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "lechatchouette@gmail.com",
                        'Name' => "Un Jeu Pour Tous"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4327553,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'title' => $title,
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }

}
