<?php
require 'vendor/autoload.php';
require 'config/app-config.php';

use \Mailjet\Resources;

global $mail;

class SubMail {

    public $conn;

    public function __construct(){
        $host=DB_HOST;
        $user=DB_USER;
        $password=DB_PASSWORD;
        $db=DB_NAME;

        $this->conn = mysqli_connect($host,$user, $password, $db);
    }
    
    public function paymentReceiptMail($str_name,$fname,$lname,$email,$amt,$rep_no,$ref_id,$pay_channel,$status,$fixed_date) {
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "hello@proddly.com",
                        'Name' => "Subscription Receipt"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $fname.' '.$lname
                        ]
                    ],
                    'TemplateID' => 3850186,
                    'TemplateLanguage' => true,
                    'Subject' => "Your Proddly Subscription Receipt",
                    'Variables' => json_decode('{
                        "date_pay": "'.$fixed_date.'",
                        "first_name": "'.$fname.'",
                        "last_name": "'.$lname.'",
                        "str_name": "'.$str_name.'",
                        "amount": "'.number_format($amt,2).'",
                        "receipt_no": "'.$rep_no.'",
                        "pay_ref": "'.$ref_id.'",
                        "p_channel": "'.$pay_channel.'",
                        "status": "'.$status.'",
                        "email_to": "'.$email.'"
                    }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        } else {
             return false;
        }
    }
    
    public function subscriptionReceiptMail($fname,$lname,$email,$plan_type,$paid_on,$next_date,$due_date) {
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "hello@proddly.com",
                        'Name' => "Subscription Receipt"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $fname.' '.$lname
                        ]
                    ],
                    'TemplateID' => 3922857,
                    'TemplateLanguage' => true,
                    'Subject' => "Your Proddly Subscription was Successful!",
                    'Variables' => json_decode('{
                        "first_name": "'.$fname.'",
                        "plan_type": "'.$plan_type.'",
                        "paid_on": "'.$paid_on.'",
                        "next_date": "'.$next_date.'",
                        "due_date": "'.$due_date.'",
                        "email_to": "'.$email.'"
                    }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        } else {
             return false;
        }
    }
    
    public function subscriptionDisabledMail($fname,$lname,$email) {
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "hello@proddly.com",
                        'Name' => "Proddly"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $fname.' '.$lname
                        ]
                    ],
                    'TemplateID' => 3912134,
                    'TemplateLanguage' => true,
                    'Subject' => "Don't leave yet. Give us another chance. ",
                    'Variables' => json_decode('{
                        "first_name": "'.$fname.'",
                        "email_to": "'.$email.'"
                    }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        } else {
             return false;
        }
    }
    
    public function subscriptionPlanChangedMail($fname,$lname,$email) {
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "hello@proddly.com",
                        'Name' => "Proddly"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $fname.' '.$lname
                        ]
                    ],
                    'TemplateID' => 3912180,
                    'TemplateLanguage' => true,
                    'Subject' => "Plan Changed Successfully ",
                    'Variables' => json_decode('{
                        "first_name": "'.$fname.'",
                        "email_to": "'.$email.'"
                    }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        } else {
             return false;
        }
    }
    
    public function subscriptionCompletedMail($fname,$lname,$email,$amount) {
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "hello@proddly.com",
                        'Name' => "Proddly"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $fname.' '.$lname
                        ]
                    ],
                    'TemplateID' => 3850248,
                    'TemplateLanguage' => true,
                    'Subject' => "Update subscription to keep your store active on Proddly  ",
                    'Variables' => json_decode('{
                        "first_name": "'.$fname.'",
                        "amount": "'.$amount.'",
                        "email_to": "'.$email.'"
                    }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        } else {
             return false;
        }
    }
    
    public function invoiceCreatedMail($fname,$lname,$email,$subscription_code,$sub_amt,$due_date) {
        require 'classes/GlobalApi.class.php';
        $sub_details = $api->curlQueryGet("https://api.paystack.co/subscription/".$subscription_code);
        if($sub_details->status == true) {
            $plan_type = $sub_details->data->plan->interval;
            $duration = substr($sub_details->data->plan->interval,0,-2);
        }
        
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "hello@proddly.com",
                        'Name' => "Proddly"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $fname.' '.$lname
                        ]
                    ],
                    'TemplateID' => 3850261,
                    'TemplateLanguage' => true,
                    'Subject' => "Your Proddly Subscription is Expiring",
                    'Variables' => json_decode('{
                        "first_name": "'.$fname.'",
                        "plan_type": "'.$plan_type.'",
                        "duration": "'.$duration.'",
                        "amount": "'.$sub_amt.'",
                        "due_date": "'.$due_date.'",
                        "email_to": "'.$email.'"
                    }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        } else {
             return false;
        }
    }
    
    public function cardExpiryMail($fname,$lname,$email,$subscription_code){
        require 'classes/GlobalApi.class.php';
        $up_card_sub = $api->curlQueryGet("https://api.paystack.co/subscription/".$subscription_code."/manage/link");
        
        if ($up_card_sub->status == true){
            $card_link = $up_card_sub->data->link;
        }
        
        $mj = new \Mailjet\Client('a3369e2ddc659c7c9ab4f66e9a3cc1bf', 'd8bd68cf2dc82ef75fe422b563c58325', true, ['version' => 'v3.1']);
            $body = ['Messages' => [[
            'From' => ['Email' => "hello@proddly.com", 'Name' => "Proddly"],
            'To' => [
                [
                    'Email' => $email,
                    'Name' => $fname." ".$lname
                ]
            ],
            'Subject' => "Update debit card information to continue seamless operations ",
            'HTMLPart' => '
                <!doctype html>
                    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
                        xmlns:o="urn:schemas-microsoft-com:office:office">
                    <head>
                        <title>Update debit card information to continue seamless operations</title>
                        <!--[if !mso]><!-->
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <!--<![endif]-->
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                        <meta name="viewport" content="width=device-width,initial-scale=1">
                        <style type="text/css">
                            #outlook a {
                                padding: 0;
                            }
                    
                            body {
                                margin: 0;
                                padding: 0;
                                -webkit-text-size-adjust: 100%;
                                -ms-text-size-adjust: 100%;
                            }
                    
                            table,
                            td {
                                border-collapse: collapse;
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                            }
                    
                            img {
                                border: 0;
                                height: auto;
                                line-height: 100%;
                                outline: none;
                                text-decoration: none;
                                -ms-interpolation-mode: bicubic;
                            }
                    
                            p {
                                display: block;
                                margin: 13px 0;
                            }
                        </style>
                        <!--[if mso]>
                            <noscript>
                            <xml>
                            <o:OfficeDocumentSettings>
                              <o:AllowPNG/>
                              <o:PixelsPerInch>96</o:PixelsPerInch>
                            </o:OfficeDocumentSettings>
                            </xml>
                            </noscript>
                            <![endif]-->
                        <!--[if lte mso 11]>
                            <style type="text/css">
                              .mj-outlook-group-fix { width:100% !important; }
                            </style>
                            <![endif]-->
                        <style type="text/css">
                            @media only screen and (min-width:480px) {
                                .mj-column-per-67 {
                                    width: 67% !important;
                                    max-width: 67%;
                                }
                    
                                .mj-column-per-33 {
                                    width: 33% !important;
                                    max-width: 33%;
                                }
                    
                                .mj-column-per-100 {
                                    width: 100% !important;
                                    max-width: 100%;
                                }
                            }
                        </style>
                        <style media="screen and (min-width:480px)">
                            .moz-text-html .mj-column-per-67 {
                                width: 67% !important;
                                max-width: 67%;
                            }
                    
                            .moz-text-html .mj-column-per-33 {
                                width: 33% !important;
                                max-width: 33%;
                            }
                    
                            .moz-text-html .mj-column-per-100 {
                                width: 100% !important;
                                max-width: 100%;
                            }
                        </style>
                        <style type="text/css">
                            [owa] .mj-column-per-67 {
                                width: 67% !important;
                                max-width: 67%;
                            }
                    
                            [owa] .mj-column-per-33 {
                                width: 33% !important;
                                max-width: 33%;
                            }
                    
                            [owa] .mj-column-per-100 {
                                width: 100% !important;
                                max-width: 100%;
                            }
                        </style>
                        <style type="text/css">
                            @media only screen and (max-width:480px) {
                                table.mj-full-width-mobile {
                                    width: 100% !important;
                                }
                    
                                td.mj-full-width-mobile {
                                    width: auto !important;
                                }
                            }
                        </style>
                    </head>
                    
                    <body style="word-spacing:normal;background-color:#F4F4F4;">
                        <div style="background-color:#F4F4F4;">
                            <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                            <div style="margin:0px auto;max-width:600px;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                                    <tbody>
                                        <tr>
                                            <td style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;text-align:center;">
                                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:402px;" ><![endif]-->
                                                <div class="mj-column-per-67 mj-outlook-group-fix"
                                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                        style="vertical-align:top;" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-size:0px;padding:0px 0px 0px 25px;padding-top:0px;padding-right:0px;padding-bottom:0px;word-break:break-word;">
                                                                    <div
                                                                        style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if mso | IE]></td><td class="" style="vertical-align:top;width:198px;" ><![endif]-->
                                                <div class="mj-column-per-33 mj-outlook-group-fix"
                                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                        style="vertical-align:top;" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-size:0px;padding:0px 25px 0px 0px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                    <div
                                                                        style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                        <p class="text-build-content"
                                                                            style="text-align: right; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                            data-testid="c7N2IT-E6038"><span
                                                                                style="color:#55575d;font-family:Arial;font-size:13px;line-height:22px;"><u>store.proddly.com</u></span>
                                                                        </p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if mso | IE]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                            <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="background:#ffffff;background-color:#ffffff;width:100%;">
                                    <tbody>
                                        <tr>
                                            <td style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;text-align:center;">
                                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                                <div class="mj-column-per-100 mj-outlook-group-fix"
                                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                        style="vertical-align:top;" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center"
                                                                    style="font-size:0px;padding:0px 0px 0px 0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;">
                                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                                        style="border-collapse:collapse;border-spacing:0px;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style="width:600px;"><img alt="" height="auto"
                                                                                        src="https://0m0ng.mjt.lu/tplimg/0m0ng/b/1kgvx/00h7.png"
                                                                                        style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                                                                        width="600"></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                    <div
                                                                        style="font-family:Arial, sans-serif;font-size:18px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                        <h1 class="text-build-content" data-testid="8vJ3U63KHGEy"
                                                                            style="margin-top: 10px; font-weight: normal;">&nbsp;</h1>
                                                                        <h1 class="text-build-content" data-testid="8vJ3U63KHGEy"
                                                                            style="margin-bottom: 10px; font-weight: normal;"><span
                                                                                style="font-family:Arial, sans-serif;font-size:18px;">Hi
                                                                                '.$fname.'</span></h1>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                    <div
                                                                        style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                        <p class="text-build-content" data-testid="Po4fth2asiVM"
                                                                            style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                            <span
                                                                                style="color:#55575d;font-family:Arial, sans-serif;font-size:13px;">We
                                                                                noticed that your card is expiring soon and we would not
                                                                                want you to experience any interruptions. </span><span
                                                                                style="color:#55575d;font-family:Arial;font-size:13px;line-height:22px;">To
                                                                                continue seamless operations on Proddly, please click the
                                                                                button below to update or replace your card
                                                                                information.&nbsp;</span></p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if mso | IE]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                            <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="background:#ffffff;background-color:#ffffff;width:100%;">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-top:0px;text-align:center;">
                                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                                <div class="mj-column-per-100 mj-outlook-group-fix"
                                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                        style="vertical-align:top;" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" vertical-align="middle"
                                                                    style="font-size:0px;padding:10px 25px 10px 25px;padding-right:25px;padding-left:25px;word-break:break-word;">
                                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                                        style="border-collapse:separate;line-height:100%;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" bgcolor="#00b0ff" role="presentation"
                                                                                    style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:10px 25px 10px 25px;background:#00b0ff;"
                                                                                    valign="middle"><a href="'.$card_link.'"
                                                                                        style="display:inline-block;background:#00b0ff;color:#ffffff;font-family:Arial, sans-serif;font-size:13px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px 10px 25px;mso-padding-alt:0px;border-radius:3px;"
                                                                                        target="_blank"><span style="font-size:14px;">Update
                                                                                            Card Information</span></a></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if mso | IE]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                            <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="background:#ffffff;background-color:#ffffff;width:100%;">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="direction:ltr;font-size:0px;padding:0px 0px 0px 0px;padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;">
                                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                                <div class="mj-column-per-100 mj-outlook-group-fix"
                                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                        style="vertical-align:top;" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                    <div
                                                                        style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                        <p class="text-build-content" data-testid="ycj3Vb5QQ"
                                                                            style="margin: 10px 0; margin-top: 10px;"><span
                                                                                style="color:#55575d;font-family:Arial, Helvetica, sans-serif;font-size:13px;">Thank
                                                                                you for being a PARTNER!</span></p>
                                                                        <p class="text-build-content" data-testid="ycj3Vb5QQ"
                                                                            style="margin: 10px 0; margin-bottom: 10px;"><span
                                                                                style="color:#55575d;font-family:Arial, Helvetica, sans-serif;font-size:13px;">Your
                                                                                Proddly Team.</span></p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if mso | IE]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#edf6f8" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                            <div style="background:#edf6f8;background-color:#edf6f8;margin:0px auto;max-width:600px;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="background:#edf6f8;background-color:#edf6f8;width:100%;">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-left:0px;padding-right:0px;text-align:center;">
                                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                                <div class="mj-column-per-100 mj-outlook-group-fix"
                                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td style="vertical-align:top;padding:0;">
                                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                                        width="100%">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center"
                                                                                    style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;">
                                                                                    <div
                                                                                        style="font-family:Arial, sans-serif;font-size:10px;letter-spacing:normal;line-height:22px;text-align:center;color:#000000;">
                                                                                        <p class="text-build-content"
                                                                                            style="text-align: center; margin: 10px 0; margin-top: 10px;"
                                                                                            data-testid="qI2QyDAZOlf2-"><span
                                                                                                style="font-size:12px;">This e-mail has been
                                                                                                sent to '.$email.'</span></p>
                                                                                        <p class="text-build-content"
                                                                                            style="text-align: center; margin: 10px 0; margin-bottom: 10px;"
                                                                                            data-testid="qI2QyDAZOlf2-"><span
                                                                                                style="font-size:12px;">Got any questions?
                                                                                                We are always happy to help. write to us at
                                                                                            </span><span
                                                                                                style="color:#00B0FF;font-size:12px;">support@proddly.com</span>
                                                                                        </p>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="center"
                                                                                    style="background:transparent;font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;">
                                                                                    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td><![endif]-->
                                                                                    <table align="center" border="0" cellpadding="0"
                                                                                        cellspacing="0" role="presentation"
                                                                                        style="float:none;display:inline-table;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td
                                                                                                    style="padding:4px;vertical-align:middle;">
                                                                                                    <table border="0" cellpadding="0"
                                                                                                        cellspacing="0" role="presentation"
                                                                                                        style="background:#000000;border-radius:50%;width:17;">
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td
                                                                                                                    style="padding:0px 0px 0px 0px;font-size:0;height:17;vertical-align:middle;width:17;">
                                                                                                                    <a href="https://www.facebook.com/sharer/sharer.php?u=[[SHORT_PERMALINK]]"
                                                                                                                        target="_blank"><img
                                                                                                                            height="17"
                                                                                                                            src="https://www.mailjet.com/images/theme/v1/icons/ico-social/facebook.png"
                                                                                                                            style="border-radius:50%;display:block;"
                                                                                                                            width="17"></a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!--[if mso | IE]></td><td><![endif]-->
                                                                                    <table align="center" border="0" cellpadding="0"
                                                                                        cellspacing="0" role="presentation"
                                                                                        style="float:none;display:inline-table;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td
                                                                                                    style="padding:4px;vertical-align:middle;">
                                                                                                    <table border="0" cellpadding="0"
                                                                                                        cellspacing="0" role="presentation"
                                                                                                        style="background:#000000;border-radius:50%;width:17;">
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td
                                                                                                                    style="padding:0px 0px 0px 0px;font-size:0;height:17;vertical-align:middle;width:17;">
                                                                                                                    <a href="https://twitter.com/intent/tweet?url=[[SHORT_PERMALINK]]"
                                                                                                                        target="_blank"><img
                                                                                                                            height="17"
                                                                                                                            src="https://www.mailjet.com/images/theme/v1/icons/ico-social/twitter.png"
                                                                                                                            style="border-radius:50%;display:block;"
                                                                                                                            width="17"></a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!--[if mso | IE]></td><td><![endif]-->
                                                                                    <table align="center" border="0" cellpadding="0"
                                                                                        cellspacing="0" role="presentation"
                                                                                        style="float:none;display:inline-table;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td
                                                                                                    style="padding:4px;vertical-align:middle;">
                                                                                                    <table border="0" cellpadding="0"
                                                                                                        cellspacing="0" role="presentation"
                                                                                                        style="background:#000000;border-radius:50%;width:17;">
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td
                                                                                                                    style="padding:0px 0px 0px 0px;font-size:0;height:17;vertical-align:middle;width:17;">
                                                                                                                    <a href="[[SHORT_PERMALINK]]"
                                                                                                                        target="_blank"><img
                                                                                                                            height="17"
                                                                                                                            src="https://www.mailjet.com/images/theme/v1/icons/ico-social/instagram.png"
                                                                                                                            style="border-radius:50%;display:block;"
                                                                                                                            width="17"></a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!--[if mso | IE]></td><td><![endif]-->
                                                                                    <table align="center" border="0" cellpadding="0"
                                                                                        cellspacing="0" role="presentation"
                                                                                        style="float:none;display:inline-table;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td
                                                                                                    style="padding:4px;vertical-align:middle;">
                                                                                                    <table border="0" cellpadding="0"
                                                                                                        cellspacing="0" role="presentation"
                                                                                                        style="background:#000000;border-radius:50%;width:17;">
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td
                                                                                                                    style="padding:0px 0px 0px 0px;font-size:0;height:17;vertical-align:middle;width:17;">
                                                                                                                    <img height="17"
                                                                                                                        src="https://www.mailjet.com/images/theme/v1/icons/ico-social/linkedin.png"
                                                                                                                        style="border-radius:50%;display:block;"
                                                                                                                        width="17"></td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!--[if mso | IE]></td></tr></table><![endif]-->
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="center"
                                                                                    style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                                    <div
                                                                                        style="font-family:Arial, sans-serif;font-size:11px;letter-spacing:normal;line-height:22px;text-align:center;color:#000000;">
                                                                                        <p class="text-build-content"
                                                                                            style="text-align: center; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                                            data-testid="JsDQq14HuN0gD">© Proddly LLC</p>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--[if mso | IE]></td></tr></table><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </div>
                    </body>
                    
                    </html>
            ',
             ]]];
            $response = $mj->post(Resources::$Email, ['body' => $body]);
            if ($response->success()) {
                return true;
            } else {
                return false;
            }
    }

    public function generatePaymentReceipt($str_name,$fname,$lname,$email,$amt,$rep_no,$ref_id,$pay_channel,$status,$fixed_date,$file_name){
        require "./classes/vendor/autoload.php";
        $date_name = date("dmY", strtotime($fixed_date));
        // $file_name = "ProddlyReceipt_".$ref_id."_".$date_name.".pdf";
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->SetTitle("Proddly Invoice ");
        $html = '
            <!doctype html>
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
                <title>Verify your Proddly email!</title>
                <!--[if !mso]><!-->
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <!--<![endif]-->
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width,initial-scale=1">
                <style type="text/css">
                    #outlook a {
                        padding: 0;
                    }

                    body {
                        margin: 0;
                        padding: 0;
                        -webkit-text-size-adjust: 100%;
                        -ms-text-size-adjust: 100%;
                    }

                    table,
                    td {
                        border-collapse: collapse;
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                    }

                    img {
                        border: 0;
                        height: auto;
                        line-height: 100%;
                        outline: none;
                        text-decoration: none;
                        -ms-interpolation-mode: bicubic;
                    }

                    p {
                        display: block;
                        margin: 13px 0;
                    }
                </style>
                <!--[if mso]>
                    <noscript>
                    <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG/>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                    </xml>
                    </noscript>
                    <![endif]-->
                <!--[if lte mso 11]>
                    <style type="text/css">
                    .mj-outlook-group-fix { width:100% !important; }
                    </style>
                    <![endif]-->
                <!--[if !mso]><!-->
                <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">
                <style type="text/css">
                    @import url(https://fonts.googleapis.com/css?family=Roboto:300,400,500,700);
                </style>
                <!--<![endif]-->
                <style type="text/css">
                    @media only screen and (min-width:480px) {
                        .mj-column-per-67 {
                            width: 67% !important;
                            max-width: 67%;
                        }

                        .mj-column-per-33 {
                            width: 33% !important;
                            max-width: 33%;
                        }

                        .mj-column-per-100 {
                            width: 100% !important;
                            max-width: 100%;
                        }

                        .mj-column-per-50 {
                            width: 50% !important;
                            max-width: 50%;
                        }
                    }
                </style>
                <style media="screen and (min-width:480px)">
                    .moz-text-html .mj-column-per-67 {
                        width: 67% !important;
                        max-width: 67%;
                    }

                    .moz-text-html .mj-column-per-33 {
                        width: 33% !important;
                        max-width: 33%;
                    }

                    .moz-text-html .mj-column-per-100 {
                        width: 100% !important;
                        max-width: 100%;
                    }

                    .moz-text-html .mj-column-per-50 {
                        width: 50% !important;
                        max-width: 50%;
                    }
                </style>
                <style type="text/css">
                    [owa] .mj-column-per-67 {
                        width: 67% !important;
                        max-width: 67%;
                    }

                    [owa] .mj-column-per-33 {
                        width: 33% !important;
                        max-width: 33%;
                    }

                    [owa] .mj-column-per-100 {
                        width: 100% !important;
                        max-width: 100%;
                    }

                    [owa] .mj-column-per-50 {
                        width: 50% !important;
                        max-width: 50%;
                    }
                </style>
                <style type="text/css">
                    @media only screen and (max-width:480px) {
                        table.mj-full-width-mobile {
                            width: 100% !important;
                        }

                        td.mj-full-width-mobile {
                            width: auto !important;
                        }
                    }
                </style>
            </head>

            <body style="word-spacing:normal;background-color:#F4F4F4;">
                <div style="background-color:#F4F4F4;">
                    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:402px;" ><![endif]-->
                                        <div class="mj-column-per-67 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:0px 0px 0px 25px;padding-top:0px;padding-right:0px;padding-bottom:0px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td><td class="" style="vertical-align:top;width:198px;" ><![endif]-->
                                        <div class="mj-column-per-33 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:0px 25px 0px 0px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                <p class="text-build-content"
                                                                    style="text-align: right; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                    data-testid="c7N2IT-E6038"><span
                                                                        style="color:#55575d;font-family:Arial;font-size:13px;line-height:22px;"><u>store.proddly.com</u></span>
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:0px 0px 0px 0px;padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="center"
                                                            style="font-size:0px;padding:0px 0px 0px 0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;">
                                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                                style="border-collapse:collapse;border-spacing:0px;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:600px;"><a href="https://proddly.com"
                                                                                target="_blank"><img alt="" height="auto"
                                                                                    src="https://0m0ng.mjt.lu/tplimg/0m0ng/b/1kgvx/00h7.png"
                                                                                    style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                                                                    width="600"></a></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:10px 0px 10px 0px;padding-bottom:10px;padding-left:0px;padding-right:0px;padding-top:10px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:300px;" ><![endif]-->
                                        <div class="mj-column-per-50 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:17px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                <h1 class="text-build-content" data-testid="PmKjwqp1H"
                                                                    style="margin-top: 10px; margin-bottom: 10px; font-weight: normal;">
                                                                    <span
                                                                        style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:17px;"><b>Payment
                                                                            Receipt</b></span></h1>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td><td class="" style="vertical-align:top;width:300px;" ><![endif]-->
                                        <div class="mj-column-per-50 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:17px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                <h1 class="text-build-content"
                                                                    style="text-align:right;; margin-top: 10px; margin-bottom: 10px; font-weight: normal;"
                                                                    data-testid="4ARyDXToT"><span
                                                                        style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:17px;">'.$fixed_date.'</span></h1>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-top:0px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:20px 25px 10px 25px;padding-top:20px;padding-right:25px;padding-bottom:10px;padding-left:25px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                <p class="text-build-content" data-testid="4Qjl5iIZ3"
                                                                    style="margin: 10px 0; margin-top: 10px;"><span
                                                                        style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;">Hi
                                                                        '.$fname.' '.$lname.',</span></p>
                                                                <p class="text-build-content"
                                                                    style="line-height: 23px; margin: 10px 0;"
                                                                    data-testid="4Qjl5iIZ3"><span
                                                                        style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;">We
                                                                        have received your monthly payment subscription for
                                                                        '.$str_name.'. To download your invoice and receipt, visit
                                                                        the subscription page on your Proddly
                                                                        dashboard.&nbsp;</span></p>
                                                                <p class="text-build-content" data-testid="4Qjl5iIZ3"
                                                                    style="margin: 10px 0; margin-bottom: 10px;"><span
                                                                        style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;">Transaction
                                                                        details:</span></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:2px 0px 2px 0px;padding-bottom:2px;padding-left:0px;padding-right:0px;padding-top:2px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                                            <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="mYWeCAsh-"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span
                                                                            style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;"><b>Total
                                                                                Charged</b></span></p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:14px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="rfuH2ojeR"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;">₦ '.$amt.'</span>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td></tr></table><![endif]-->
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:2px 0px 2px 0px;padding-bottom:2px;padding-left:0px;padding-right:0px;padding-top:2px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                                            <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="H0bdX4MaPG"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span  style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;"><b>Receipt No.</b></span></p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:14px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="N3OenG-kKm"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;">'.$rep_no.'</span>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td></tr></table><![endif]-->
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:2px 0px 2px 0px;padding-bottom:2px;padding-left:0px;padding-right:0px;padding-top:2px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                                            <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="DqWB4DIVKD"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;"><b>Payment Reference</b></span>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:14px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="wO_2at8lFv" style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">'.$ref_id.'</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td></tr></table><![endif]-->
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:2px 0px 2px 0px;padding-bottom:2px;padding-left:0px;padding-right:0px;padding-top:2px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                                            <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="kTe5f-Va-d"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;"><b>Payment Channel</b></span>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:14px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="XTk7QNeI71" style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">'.$pay_channel.'</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td></tr></table><![endif]-->
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:2px 0px 2px 0px;padding-bottom:2px;padding-left:0px;padding-right:0px;padding-top:2px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                                            <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="CcwWpOfGIg"
                                                                        style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">
                                                                        <span style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;"><b>Status</b></span>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td><td style="vertical-align:top;width:300px;" ><![endif]-->
                                            <div class="mj-column-per-50 mj-outlook-group-fix"
                                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:50%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                    style="vertical-align:top;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left"
                                                                style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                <div
                                                                    style="font-family:Arial, sans-serif;font-size:14px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                    <p class="text-build-content" data-testid="TN0BbTgqJf" style="margin: 10px 0; margin-top: 10px; margin-bottom: 10px;">'.$status.'</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--[if mso | IE]></td></tr></table><![endif]-->
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#ffffff;background-color:#ffffff;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-left:0px;padding-right:0px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                style="vertical-align:top;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                <p class="text-build-content"
                                                                    style="line-height: 15px; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                    data-testid="GZKO96WX4"><span
                                                                        style="color:#55575d;font-family:Roboto, Helvetica, Arial, sans-serif;font-size:13px;">Thank
                                                                        you for being a PARTNER!&nbsp;</span></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;">
                                                                <p class="text-build-content"
                                                                    style="line-height: 14px; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                    data-testid="fEgrWsfGI"><span style="color:#55575d;font-family:Arial, Helvetica, sans-serif;font-size:13px;">&nbsp;Your
                                                                        Proddly Team.</span></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#edf6f8" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                    <div style="background:#edf6f8;background-color:#edf6f8;margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="background:#edf6f8;background-color:#edf6f8;width:100%;">
                            <tbody>
                                <tr>
                                    <td
                                        style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-left:0px;padding-right:0px;text-align:center;">
                                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                                        <div class="mj-column-per-100 mj-outlook-group-fix"
                                            style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td style="vertical-align:top;padding:0;">
                                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                                width="100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td align="center"
                                                                            style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;">
                                                                            <div
                                                                                style="font-family:Arial, sans-serif;font-size:10px;letter-spacing:normal;line-height:22px;text-align:center;color:#000000;">
                                                                                <p class="text-build-content"
                                                                                    style="text-align: center; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                                    data-testid="qI2QyDAZOlf2-"><span
                                                                                        style="font-size:12px;">Got any questions?
                                                                                        We are always happy to help. write to us at
                                                                                    </span><span
                                                                                        style="color:#00B0FF;font-size:12px;">support@proddly.com</span>
                                                                                </p>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center"
                                                                            style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                                            <div
                                                                                style="font-family:Arial, sans-serif;font-size:11px;letter-spacing:normal;line-height:22px;text-align:center;color:#000000;">
                                                                                <p class="text-build-content"
                                                                                    style="text-align: center; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"
                                                                                    data-testid="JsDQq14HuN0gD">© Proddly LLC</p>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if mso | IE]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><![endif]-->
                </div>
            </body>

            </html>
        ';
        $mpdf->WriteHTML($html);
        $mpdf->Output("public/receipt/".$file_name);
    }

    public function get_store_details($store_id){
        $str_query = "SELECT * FROM tbl_store_account WHERE store_id='$store_id'";
        $str_obj = $this->conn->prepare($str_query);
        if ($str_obj->execute()) {
            $data = $str_obj->get_result();
            return $data->fetch_assoc();
        }
        return array();
    }
}

$mail = new SubMail();
?>