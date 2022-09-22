<?php
ini_set('max_execution_time', 999999);
ini_set('memory_limit','999999M');
ini_set('upload_max_filesize', '500M');
ini_set('max_input_time', '-1');
ini_set('max_execution_time', '-1');

  header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization,Developer_Key");

require ABSPATH.'/classes/vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Mailjet\Resources;
use \PhpOffice\PhpSpreadsheet\Reader\IReader;
date_default_timezone_set('Africa/Lagos'); // WAT

function app_db () {
    include_once ABSPATH.'/config/app-config.php';

    $db_conn = array(
        'host' => DB_HOST, 
        'user' => DB_USER,
        'pass' => DB_PASSWORD,
        'database' => DB_NAME, 
    );
    $db = new SimpleDBClass($db_conn);
    return $db;     
}

function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getDevAccessKeyHeader(){
    $headers = null;
    if (isset($_SERVER['Developer_Key_key'])) {
        $headers = trim($_SERVER["Developer_Key"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Developer_Key'])) {
            $headers = trim($requestHeaders['Developer_Key']);
        }
    }
    return $headers;
}

function getBearerToken() {
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function getDeveloperKey() {
    $headers = getDevAccessKeyHeader();
    if (!empty($headers)) {
         return $headers;
    }
    return null;
}

function clean($string) {
    $string = str_replace(' ', '_', $string);

    return preg_replace('/[^A-Za-z0-9._\-]/', '', $string);
}

function save_base64_image($base64_image_string, $output_file_without_extension, $path_with_end_slash="public/uploads/store_products/" ) {
    $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
    $mime=$splited[0];
    $data=$splited[1];

    $mime_split_without_base64=explode(';', $mime,2);
    $mime_split=explode('/', $mime_split_without_base64[0],2);
    if(count($mime_split)==2) {
        $extension=$mime_split[1];
        if($extension=='jpeg')$extension='jpg';
        $output_file_with_extension=$output_file_without_extension.'.'.$extension;
    }
    file_put_contents( $path_with_end_slash . $output_file_with_extension, base64_decode($data) );
    return $output_file_with_extension;
}

function guidv4($data = null) {
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s%s', str_split(bin2hex($data), 4));
}

$router->map( 'GET', '/', function() {
	$ajax_url = AJAX_URL;
	include  ABSPATH.'/views/index.php';
});

$router->map( 'POST', '/v1/api/create-user', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $first_name = isset($data->usr_fullname)?$data->usr_fullname:"";
        $email = isset($data->usr_email)?$data->usr_email:"";
        $party = isset($data->usr_party)?$data->usr_party:"";
        $gender = isset($data->usr_party)?$data->usr_gender:"";
        $password = isset($data->usr_password)?password_hash($data->usr_password, PASSWORD_DEFAULT):"";

        if (!empty($first_name)&&!empty($email)&&!empty($party)&&!empty($gender)&&!empty($password)) {
            $ch0 = $db->select("select * from tbl_users where usr_email='" . $db->CleanDBData($email) . "'");
            if ($ch0 > 0) {
                http_response_code(400);
                echo json_encode(array('status' => 'error', 'msg' => 'Store email already exist, proceed to login'));
            } else {
                $insert_arrays = array
                (
                    'usr_id' => rand(1000000, 9999999),
                    'usr_fullname' => $db->CleanDBData($data->usr_fullname),
                    'usr_email' => $db->CleanDBData($data->usr_email),
                    'usr_party' => $db->CleanDBData($data->usr_party),
                    'usr_gender' => $db->CleanDBData($data->usr_gender),
                    'usr_password' => $db->CleanDBData($password),
                    'usr_created_at' => date("Y-m-d H:i:s")
                );
                $q0 = $db->Insert('tbl_users', $insert_arrays);
                if ($q0 > 0) {
                    http_response_code(200);
                    echo json_encode(array("status" => 'success', "message" => "Account created successfully, proceed to login",));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => 'error', "message" => "Unable to register user, pls try again later."));
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => 'error', "message" => "Kindly fill all required fields."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/user-login', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $ch0 = $db->select("select * from tbl_users where usr_email='".$db->CleanDBData($data->usr_email)."'");
        if ($ch0 <= 0) {
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'msg' => 'User email not found',));
        } else {
            $password_used = $ch0[0]['usr_password'];
            if (password_verify($data->usr_password,$password_used)) {
                $iss = 'localhost';
                $iat = time();
                $nbf = $iat; // issued after 1 secs of been created
                $exp = $iat + (86400 * 1); // expired after 1day/24hrs of been created
                $aud = "lrp_user"; //the type of audience e.g. admin or client

                $secret_key = getenv('HTTP_MY_SECRET');
                $payload = array(
                    "iss"=>$iss,"iat"=>$iat,"nbf"=>$nbf,"exp"=>$exp,"aud"=>$aud,
                    "usr_id"=>$ch0[0]['usr_id'],
                    "usr_fullname"=>$ch0[0]['usr_fullname'],
                    "usr_email"=>$ch0[0]['usr_email'],
                    "usr_party"=>$ch0[0]['usr_party'],
                    "usr_gender"=>$ch0[0]['usr_gender'],
                    "usr_created_at"=>$ch0[0]['usr_created_at']
                );
                $jwt = JWT::encode($payload, $secret_key, 'HS512');
                http_response_code(200);
                echo json_encode(array("status" => 'success', "jwt" => $jwt, "message" => "User logged in successfully",));
            } else {
                http_response_code(400);
                echo json_encode(array("status" => 'error', "message" => "Incorrect password, try again."));
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/forgot-password', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $ch0 = $db->select("select * from tbl_users where usr_email='".$db->CleanDBData($data->usr_email)."'");
        if ($ch0 <= 0) {
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'msg' => 'Email not found',));
        } else {
            $email = $data->usr_email;
            $usr_fullname = $ch0[0]['usr_fullname'];

            $selector = bin2hex(random_bytes(4));
            $token = random_bytes(15);

            $host = "https://$_SERVER[HTTP_HOST]";
            $url= $host."/reset-password/".$selector."/".bin2hex($token);
            $expires = date("U") + 1200;

            //Delete any existing user token entry
            $db->Qry("DELETE FROM tbl_pwd_reset WHERE reset_email='$email'");
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            $insert_arrays = array
            (
                'reset_email' => $db->CleanDBData($email),
                'reset_selector' => $db->CleanDBData($selector),
                'reset_token' => $db->CleanDBData($hashedToken),
                'reset_expires' => $db->CleanDBData($expires)
            );
            $q0 = $db->Insert('tbl_pwd_reset', $insert_arrays);
            if ($q0 > 0) {
                $mj = new \Mailjet\Client('9818c2b9ffcc649ab1024e1531198262', 'b378296f762bcc09fb0122c3abce71ab', true, ['version' => 'v3.1']);
                $body = ['Messages' => [[
                    'From' => ['Email' => "support@mainlandcode.com", 'Name' => "TEP DIGITAL LRP"],
                    'To' => [
                        [
                            'Email' => $email,
                        ]
                    ],
                    'Subject' => "TEP DIGITAL LRP PASSWORD RESET",
                    'HTMLPart' => '
                    <!doctype html>
                    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><title>PASSWORD RESET</title><!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style type="text/css">#outlook a { padding:0; }
                          body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
                          table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
                          img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
                          p { display:block;margin:13px 0; }</style><!--[if mso]>
                        <noscript>
                        <xml>
                        <o:OfficeDocumentSettings>
                          <o:AllowPNG/>
                          <o:PixelsPerInch>96</o:PixelsPerInch>
                        </o:OfficeDocumentSettings>
                        </xml>
                        </noscript>
                        <![endif]--><!--[if lte mso 11]>
                        <style type="text/css">
                          .mj-outlook-group-fix { width:100% !important; }
                        </style>
                        <![endif]--><style type="text/css">@media only screen and (min-width:480px) {
                        .mj-column-per-100 { width:100% !important; max-width: 100%; }
                      }</style><style media="screen and (min-width:480px)">.moz-text-html .mj-column-per-100 { width:100% !important; max-width: 100%; }</style><style type="text/css">[owa] .mj-column-per-100 { width:100% !important; max-width: 100%; }</style><style type="text/css">@media only screen and (max-width:480px) {
                      table.mj-full-width-mobile { width: 100% !important; }
                      td.mj-full-width-mobile { width: auto !important; }
                    }</style></head><body style="word-spacing:normal;background-color:#F4F4F4;"><div style="background-color:#F4F4F4;"><!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div style="margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:0px 0 0px 0;padding-bottom:0px;padding-top:0px;text-align:center;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"><tbody><tr><td style="vertical-align:top;padding:0 0 0 0;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"><tbody><tr><td align="center" vertical-align="top" style="font-size:0px;padding:30px 25px 40px 25px;padding-top:30px;padding-right:25px;padding-bottom:30px;padding-left:25px;word-break:break-word;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"><tbody><tr><td style="width:335px;"><img alt="" height="auto" src="https://0unk4.mjt.lu/tplimg/0unk4/b/l1wg2/tw6t.png" style="border:none;border-radius:px;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="335"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#FFFFFF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div style="background:#FFFFFF;background-color:#FFFFFF;margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;background-color:#FFFFFF;width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:0px 0 0px 0;padding-bottom:0px;padding-top:0px;text-align:center;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:middle;width:600px;" ><![endif]--><div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:middle;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"><tbody><tr><td style="vertical-align:middle;padding:0 0 0 0;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"><tbody><tr><td align="left" style="font-size:0px;padding:25px 25px 0 25px;padding-top:25px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;"><div style="font-family:Arial, sans-serif;font-size:25px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;"><p class="text-build-content" style="text-align: center; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;" data-testid="z_hC_R5X1-tN"><span style="color:#292929;font-family:Arial;font-size:25px;line-height:32px;"><b>Forgot Password</b></span></p></div></td></tr><tr><td align="left" style="font-size:0px;padding:0 25px 20px 25px;padding-top:0px;padding-right:25px;padding-bottom:20px;padding-left:25px;word-break:break-word;"><div style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;"><p class="text-build-content" style="line-height: 25px; margin: 10px 0; margin-top: 10px;" data-testid="VNJ2orKLIAyN">Hello '.$usr_fullname.',&nbsp;</p><p class="text-build-content" style="line-height: 23px; margin: 10px 0;" data-testid="VNJ2orKLIAyN"><span style="font-size:14px;">This e-mail has been sent to you because you could not remember the password for your <b>TEP DIGITAL LRP account</b>. No worries. We\'ve got you!</span></p><p class="text-build-content" style="line-height: 23px; margin: 10px 0; margin-bottom: 10px;" data-testid="VNJ2orKLIAyN"><span style="font-size:14px;">Please click the button below to reset your password.</span></p></div></td></tr><tr><td align="center" vertical-align="middle" style="background:transparent;font-size:0px;padding:0px 25px 40px 25px;padding-top:0px;padding-right:25px;padding-bottom:40px;padding-left:25px;word-break:break-word;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;line-height:100%;"><tbody><tr><td align="center" bgcolor="#73cf48" role="presentation" style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:10px 25px 10px 25px;background:#73cf48;" valign="middle"><a href="'.$url.'" style="display:inline-block;background:#73cf48;color:#ffffff;font-family:Arial, sans-serif;font-size:13px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px 10px 25px;mso-padding-alt:0px;border-radius:3px;" target="_blank"><span style="font-size:14px;"><b>Reset Password</b></span></a></td></tr></tbody></table></td></tr><tr><td align="left" style="font-size:0px;padding:0 25px 20px 25px;padding-top:0px;padding-right:25px;padding-bottom:20px;padding-left:25px;word-break:break-word;"><div style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;"><p class="text-build-content" style="line-height: 25px; margin: 10px 0; margin-top: 10px;" data-testid="ozMdPEO7n">If you have any trouble clicking the button above, please copy and place the URL below in your web browser.</p><p class="text-build-content" style="line-height: 25px; margin: 10px 0;" data-testid="ozMdPEO7n">'.$url.'</p><p class="text-build-content" style="line-height: 23px; margin: 10px 0; margin-bottom: 10px;" data-testid="ozMdPEO7n">&nbsp;</p></div></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div style="margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tbody><tr><td align="left" style="background:#ffffff;font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:25px;padding-bottom:0px;padding-left:25px;word-break:break-word;"><div style="font-family:Arial, sans-serif;font-size:13px;letter-spacing:normal;line-height:1;text-align:left;color:#000000;"><p class="text-build-content" style="text-align: center; margin: 10px 0; margin-top: 10px;" data-testid="l50PVXOAC"><span style="font-family:Arial;">This e-mail has been sent to '.$email.'</span></p><p class="text-build-content" style="text-align: center; margin: 10px 0;" data-testid="l50PVXOAC">&nbsp;</p><p class="text-build-content" style="text-align: center; margin: 10px 0;" data-testid="l50PVXOAC"><span style="font-family:Arial;">Got any questions? We are always happy to help. write to us at support@mainlandcode.com</span></p><p class="text-build-content" style="text-align: center; margin: 10px 0;" data-testid="l50PVXOAC">&nbsp;</p><p class="text-build-content" style="text-align: center; margin: 10px 0; margin-bottom: 10px;" data-testid="l50PVXOAC"><span style="font-family:Arial;">© Digital LRP</span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><![endif]--></div>
                    </body></html>
                    ',
                ]]];
                $response = $mj->post(Resources::$Email, ['body' => $body]);
                if ($response->success()) {
                    http_response_code(200);
                    echo json_encode(array('status' => 'success', 'msg' => 'Reset email sent'));
                } else {
                    http_response_code(400);
                    echo json_encode(array('status' => 'error', 'msg' => 'Unable to send reset mail.'));
                }
            } else {
                http_response_code(400);
                echo json_encode(array('status' => 'error', 'msg' => 'Unable to send reset mail.'));
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/reset-password', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $currentDate = date("U");
        if (isset($data->reset_selector) && !empty($data->reset_selector) && !empty($data->usr_password)) {
            $reset_selector = $data->reset_selector;
            $p_reset_q = $db->select("SELECT * FROM tbl_pwd_reset WHERE reset_selector='$reset_selector' AND reset_expires >= $currentDate");
            if ($p_reset_q > 0 ) {
                $reset_email = $p_reset_q[0]['reset_email'];
                $update_fields = array('usr_password' => password_hash($data->usr_password, PASSWORD_DEFAULT));
                $array_where = array('usr_email' => $db->CleanDBData($reset_email));
                $update_query = $db->Update('tbl_users', $update_fields, $array_where);
                if ($update_query > 0) {
                    $db->Qry("DELETE FROM tbl_pwd_reset WHERE reset_email='$reset_email'");
                    http_response_code(200);
                    echo json_encode(array('status' => 'success','msg'=>'Password successfully changed. Proceed to login'));
                } else {
                    http_response_code(400);
                    echo json_encode(array('status' => 'error','msg'=>'Error while trying to reset your password, contact our support for help.'));
                }
            } else {
                http_response_code(400);
                echo json_encode(array('status' => 'error', 'msg' => 'Invalid reset token and/or expired reset link'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'msg' => 'Kindly provide the reset selector & new password key to update password'));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/update-user-profile', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();

        $usr_fullname = isset($data->usr_fullname)?$data->usr_fullname:"";
        $usr_gender = isset($data->usr_gender)?$data->usr_gender:"";

        if (!empty($usr_fullname)&&!empty($usr_gender)) {
            try {
                $secret_key = getenv('HTTP_MY_SECRET');
                $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
                $usr_id = $decoded_data->usr_id;

                $update_fields = array(
                    'usr_fullname' => $db->CleanDBData($usr_fullname),
                    'usr_gender' => $db->CleanDBData($usr_gender)
                );
                $array_where = array(
                    'usr_id' => $db->CleanDBData($usr_id)
                );
                $q0 = $db->Update('tbl_users', $update_fields, $array_where);

                if ($q0 > 0) {
                    http_response_code(200);
                    echo json_encode(array('status' => 'success', 'msg' => 'record updated', "usr_id" => $usr_id));
                } else {
                    http_response_code(400);
                    echo json_encode(array('status' => 'error', 'msg' => 'cannot update record'));
                }
            } catch (Exception $ex) {
                http_response_code(400);
                echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => 'error', "message" => "Kindly fill all required fields."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/update-user-password', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            if(!empty(trim($data->current_password)) && !empty(trim($data->new_password)) && !empty(trim($data->confirm_password))) {
                $ch0 = $db->select("select * from tbl_users where usr_id='$usr_id'");
                $password_used = $ch0[0]['usr_password'];
                if (password_verify($data->current_password,$password_used)) {
                    if ($data->new_password == $data->confirm_password) {
                        if ($data->current_password != $data->new_password) {
                            $acct_info_update_fields = array('usr_password' => $db->CleanDBData(password_hash($data->new_password, PASSWORD_DEFAULT)));
                            $acct_info_array_where = array('usr_id' => $db->CleanDBData($usr_id));
                            $q0 = $db->Update('tbl_users', $acct_info_update_fields, $acct_info_array_where);

                            if ($q0 > 0) {
                                http_response_code(200);
                                echo json_encode(array('status' => 'success', 'msg' => 'User account password successfully updated'));
                            } else {
                                http_response_code(400);
                                echo json_encode(array('status' => 'error', 'msg' => 'Unable to update password, try again later'));
                            }
                        } else {
                            http_response_code(400);
                            echo json_encode(array('status' => 'error', 'msg' => 'Password is currently inuse, try another password'));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array('status' => 'error', 'msg' => 'Unmatched new password combination'));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array('status' => 'error', 'msg' => 'Old password entered seems to be incorrect'));
                }
            } else {
                http_response_code(400);
                echo json_encode(array('status' => 'error', 'msg' => 'One or more required field empty'));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'GET', '/v1/api/fetch-user-by-id[*:action]', function($user_id) {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $q0 = $db->select("select * from tbl_users where usr_id='$user_id'");
            if($q0 > 0) {
                http_response_code(200);
                echo json_encode(array('status'=>'success','data'=>$q0,'msg' => 'found records'));
            } else {
                http_response_code(400);
                echo json_encode(array('status'=>'error', 'data'=>array(), 'msg' => 'no user found',));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'GET', '/v1/api/fetch-account-occupants', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $q0 = $db->select("select * from tbl_occupants where usr_id='".$usr_id."' ");
            $q0Count = $db->select("select count(*) as total from tbl_occupants where usr_id='".$usr_id."' ");
            if($q0 > 0) {
                http_response_code(200);
                echo json_encode(array('status'=>'success','data'=>$q0,'total_count'=>$q0Count[0],'msg' => 'found records'));
            } else {
                http_response_code(200);
                echo json_encode(array('status'=>'error', 'data'=>array(), 'msg' => 'no product found',));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/add-new-occupant', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $acc_name = isset($data->occ_name)?$data->occ_name:"";
            $acc_state = isset($data->occ_state)?$data->occ_state:"";
            $acc_age = isset($data->occ_age)?$data->occ_age:"";
            $acc_gender = isset($data->occ_gender)?$data->occ_gender:"";

            if (!empty($acc_name)&&!empty($acc_state)&&!empty($acc_age)&&!empty($acc_gender)) {
                $ch0 = $db->select("select * from tbl_occupants where occ_name='" . $db->CleanDBData($acc_name) . "'");
                if ($ch0 > 0) {
                    http_response_code(400);
                    echo json_encode(array('status' => 'error', 'msg' => 'Respondent name already exist.'));
                } else {
                    $insert_arrays = array
                    (
                        'usr_id' => $usr_id,
                        'occ_id' => rand(1000,9999),
                        'occ_name' => $db->CleanDBData($acc_name),
                        'occ_state' => $db->CleanDBData($acc_state),
                        'occ_age' => $db->CleanDBData($acc_age),
                        'occ_gender' => $db->CleanDBData($acc_gender),
                        'occ_created_on' => date("Y-m-d H:i:s")
                    );
                    $q0 = $db->Insert('tbl_occupants', $insert_arrays);
                    if ($q0 > 0) {
                        http_response_code(200);
                        echo json_encode(array("status" => 'success', "message" => "Occupant created successfully",));
                    } else {
                        http_response_code(400);
                        echo json_encode(array("status" => 'error', "message" => "Unable to create Occupant, pls try again."));
                    }
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status" => 'error', "message" => "Kindly fill all required fields."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'GET', '/v1/api/fetch-occupant-by-id/[*:action]', function($occupant_id) {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    $occupant_id =  $db->CleanDBData($occupant_id);

    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $q0 = $db->select("select * from tbl_occupants where occ_id ='$occupant_id' and usr_id='$usr_id'");
            if($q0 > 0) {
                http_response_code(200);
                echo json_encode(array('status'=>'success','data'=>$q0,'msg' => 'found records'));
            } else {
                http_response_code(400);
                echo json_encode(array('status'=>'error', 'msg' => 'no records found',));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/update-occupant-account', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();

        $acc_name = isset($data->occ_name)?$data->occ_name:"";
        $acc_state = isset($data->occ_state)?$data->occ_state:"";
        $acc_age = isset($data->occ_age)?$data->occ_age:"";
        $acc_gender = isset($data->occ_gender)?$data->occ_gender:"";
        $occ_id = isset($data->occ_id)?$data->occ_id:"";

        if (!empty($acc_name)&&!empty($acc_state)&&!empty($acc_age)&&!empty($acc_gender)) {
            try {
                $secret_key = getenv('HTTP_MY_SECRET');
                $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
                $usr_id = $decoded_data->usr_id;

                $update_fields = array(
                    'occ_name' => $db->CleanDBData($acc_name),
                    'occ_state' => $db->CleanDBData($acc_state),
                    'occ_age' => $db->CleanDBData($acc_age),
                    'occ_gender' => $db->CleanDBData($acc_gender)
                );
                $array_where = array(
                    'occ_id' => $db->CleanDBData($occ_id),
                    'usr_id' => $db->CleanDBData($usr_id)
                );
                $q0 = $db->Update('tbl_occupants', $update_fields, $array_where);

                if ($q0 > 0) {
                    http_response_code(200);
                    echo json_encode(array('status' => 'success', 'msg' => 'record updated', "occ_id" => $data->occ_id));
                } else {
                    http_response_code(400);
                    echo json_encode(array('status' => 'error', 'msg' => 'cannot update record'));
                }
            } catch (Exception $ex) {
                http_response_code(400);
                echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => 'error', "message" => "Kindly fill all required fields."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/delete-occupant', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $occ_id = $db->CleanDBData($data->occ_id);
            $array_where = array(
                'occ_id' => $db->CleanDBData($occ_id),
                'usr_id' => $db->CleanDBData($usr_id)
            );
            $Qry = $db->Delete('tbl_occupants',$array_where);
            if($Qry) {
                http_response_code(200);
                echo json_encode(array('status'=>'success','msg' => 'occupant deleted successfully'));
            } else {
                http_response_code(400);
                echo json_encode(array('status'=>'error', 'msg' => 'unable to delete occupant',));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/start-game-session', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = guidv4();
            $occupant_id = isset($data->occ_id)?$data->occ_id:"";
            $test_type = isset($data->game_type)?$data->game_type:"";
            $gms_start_at = date("Y-m-d H:i:s");

            if ($test_type != "Literacy" || $test_type != "Numeracy") {
                if (!empty($usr_id) && !empty($session_id) && !empty($occupant_id) && !empty($test_type) && !empty($gms_start_at)) {
                    $insert_arrays = array
                    (
                        'gms_id' => $db->CleanDBData($session_id),
                        'usr_id' => $db->CleanDBData($usr_id),
                        'occ_id' => $db->CleanDBData($occupant_id),
                        'gms_type' => $db->CleanDBData($test_type),
                        'gms_start_at' => date("Y-m-d H:i:s")
                    );
                    $q0 = $db->Insert('tbl_game_sessions', $insert_arrays);
                    if ($q0 > 0) {
                        $db->Insert('tbl_game_results',array("gr_gms_id"=>$session_id,"gr_usr_id"=>$usr_id,"gr_occ_id"=>$occupant_id));
                        http_response_code(200);
                        echo json_encode(array(
                            "status"=>"success","session_id"=>$session_id,"game_type"=>$test_type,
                            "message" => "Game initiated successfully"
                        ));
                    } else {
                        http_response_code(400);
                        echo json_encode(array("status"=>'error',"message" => "Unable to create Occupant, pls try again."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("status"=>'error',"message" => "Kindly Provide Occupant ID & Test Type."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Type can only be Literacy or Numeracy."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'POST', '/v1/api/submit-letter-stage-1-OLD', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = isset($data->session_id)?$data->session_id:"";
            $result_obj = isset($data->result)?json_encode($data->result):"";

            // print_r(json_encode($result_obj)); die();
            if (!empty($session_id)) {
                if ($result_obj !="") {
                    $res_arr = array('letter_stage_1'=>$result_obj);
                    $q0 = $db->Update('tbl_game_results',$res_arr,array('gr_gms_id'=>$session_id,'gr_usr_id'=>$usr_id));
                    if ($q0 > 0) {
                        http_response_code(200);
                        echo json_encode(array(
                            "status"=>"success","session_id"=>$session_id,
                            "message" => "Letter Level stage One (1) saved successfully"
                        ));
                    } else {
                        http_response_code(400);
                        echo json_encode(array("status"=>'error',"message" => "Unable to create Occupant, pls try again."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("status"=>'error',"message" => "Results object cannot be empty."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing/Empty Session ID Parameter in body request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'POST', '/v1/api/submit-letter-stage-1', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = isset($data->session_id)?$data->session_id:"";
            $answer = isset($data->answer)?$data->answer:"";
            $data_obj = isset($data->data)?json_encode($data->data):"";

            // print_r(json_encode($data_obj)); die();
            if (!empty($session_id) && !empty($session_id)) {
                if ($data_obj !="") {
                    $res_arr = array('ls_1_ans'=>$answer,'ls_1_data'=>$data_obj);
                    $q0 = $db->Update('tbl_game_results',$res_arr,array('gr_gms_id'=>$session_id,'gr_usr_id'=>$usr_id));
                    if ($q0 > 0) {
                        http_response_code(200);
                        echo json_encode(array(
                            "status"=>"success","session_id"=>$session_id,
                            "message" => "Letter Level stage One (1) saved successfully"
                        ));
                    } else {
                        http_response_code(400);
                        echo json_encode(array("status"=>'error',"message" => "Unable to save result, pls try again."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("status"=>'error',"message" => "Results object cannot be empty."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing/Empty Session ID Parameter in body request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'GET', '/v1/api/fetch-user-game-result', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            if (!empty($usr_id)) {
                $q0 = $db->select("select gs.gms_type,gs.gms_id,oc.occ_name,oc.occ_age,oc.occ_gender from tbl_game_sessions gs 
                                                inner join tbl_occupants oc on oc.occ_id = gs.occ_id 
                                                where gs.usr_id='$usr_id'");

                if($q0 > 0) {
                    $result_arr = array();
                    foreach ($q0 as $row) {
                        $q1 = $db->select("select * from tbl_game_results where gr_gms_id='".$row['gms_id']."' ");

                        $ls_1 = !empty($q1[0]['ls_1_ans'])?$q1[0]['ls_1_ans']:0;
                        $ls_2 = !empty($q1[0]['ls_2_ans'])?$q1[0]['ls_2_ans']:0;
                        $ls_3 = !empty($q1[0]['ls_3_ans'])?$q1[0]['ls_3_ans']:0;

                        $ws_1 = !empty($q1[0]['ws_1_ans'])?$q1[0]['ws_1_ans']:0;
                        $ws_2 = !empty($q1[0]['ws_2_ans'])?$q1[0]['ws_2_ans']:0;
                        $ws_3 = !empty($q1[0]['ws_3_ans'])?$q1[0]['ws_3_ans']:0;
                        $ws_4 = !empty($q1[0]['ws_4_ans'])?$q1[0]['ws_4_ans']:0;

                        $ps_1 = !empty($q1[0]['ps_1_ans'])?$q1[0]['ps_1_ans']:0;
                        $ps_2 = !empty($q1[0]['ps_2_ans'])?$q1[0]['ps_2_ans']:0;
                        $ps_3 = !empty($q1[0]['ps_3_ans'])?$q1[0]['ps_3_ans']:0;
                        $ps_4 = !empty($q1[0]['ps_4_ans'])?$q1[0]['ps_4_ans']:0;

                        $ss_1 = !empty($q1[0]['ss_1_ans'])?$q1[0]['ss_1_ans']:0;

                        $total = $ls_1 + $ls_2 + $ls_3 + $ws_1 + $ws_2 + $ws_3 + $ws_4 + $ps_1 + $ps_2 + $ps_3 + $ps_4 + $ss_1;
                        $percen = ($total/25)*100;
                        $format_percent = number_format($percen,2)."%";
                        $status = ($total == 100)?'Completed':'Incomplete';

                        $result_arr[] = array(
                            "session_id" => $row['gms_id'],
                            "gms_type" => $row['gms_type'],
                            "occ_name" => $row['occ_name'],
                            "occ_age" => $row['occ_age'],
                            "occ_gender" => $row['occ_gender'],
                            "total_score" => $total,
                            "score_percent" => $format_percent,
                            "status" => $status
                        );
                    }

                    http_response_code(200);
                    echo json_encode(array('status'=>'success','data'=>$result_arr,'msg' => 'found records'));

                } else {
                    http_response_code(200);
                    echo json_encode(array('status'=>'error', 'data'=>array(), 'msg' => 'no result found',));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing User ID in Token request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'GET', '/v1/api/fetch-game-result/[*:action]', function($session_id) {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = isset($session_id)?$session_id:"";

            // print_r(json_encode($result_obj)); die();
            if (!empty($session_id)) {
                $q1 = $db->select("select gr.*,gs.gms_type,oc.occ_name,oc.occ_age,oc.occ_gender from tbl_game_results gr 
                                                inner join tbl_game_sessions gs on gs.gms_id = gr.gr_gms_id 
                                                inner join tbl_occupants oc on oc.occ_id = gr.gr_occ_id 
                                                where gr.gr_gms_id='".$session_id."' ");
                if($q1 > 0) {
                    $ls_1 = !empty($q1[0]['ls_1_ans'])?$q1[0]['ls_1_ans']:0;
                    $ls_2 = !empty($q1[0]['ls_2_ans'])?$q1[0]['ls_2_ans']:0;
                    $ls_3 = !empty($q1[0]['ls_3_ans'])?$q1[0]['ls_3_ans']:0;

                    $ws_1 = !empty($q1[0]['ws_1_ans'])?$q1[0]['ws_1_ans']:0;
                    $ws_2 = !empty($q1[0]['ws_2_ans'])?$q1[0]['ws_2_ans']:0;
                    $ws_3 = !empty($q1[0]['ws_3_ans'])?$q1[0]['ws_3_ans']:0;
                    $ws_4 = !empty($q1[0]['ws_4_ans'])?$q1[0]['ws_4_ans']:0;

                    $ps_1 = !empty($q1[0]['ps_1_ans'])?$q1[0]['ps_1_ans']:0;
                    $ps_2 = !empty($q1[0]['ps_2_ans'])?$q1[0]['ps_2_ans']:0;
                    $ps_3 = !empty($q1[0]['ps_3_ans'])?$q1[0]['ps_3_ans']:0;
                    $ps_4 = !empty($q1[0]['ps_4_ans'])?$q1[0]['ps_4_ans']:0;

                    $ss_1 = !empty($q1[0]['ss_1_ans'])?$q1[0]['ss_1_ans']:0;

                    $total = $ls_1 + $ls_2 + $ls_3 + $ws_1 + $ws_2 + $ws_3 + $ws_4 + $ps_1 + $ps_2 + $ps_3 + $ps_4 + $ss_1;
                    $percen = ($total/25)*100;
                    $format_percent = number_format($percen,2)."%";
                    $status = ($total == 100)?'Completed':'Incomplete';

                    $res = array(
                        "session_id" => $q1[0]['gr_gms_id'],
                        "occ_id" => $q1[0]['gr_occ_id'],
                        "occ_name" => $q1[0]['occ_name'],
                        "occ_age" => $q1[0]['occ_age'],
                        "occ_gender" => $q1[0]['occ_gender'],
                        "gms_type" => $q1[0]['gms_type'],
                        "total_score" => $total,
                        "score_percent" => $format_percent,
                        "status" => $status
                    );

                    http_response_code(200);
                    echo json_encode(array('status'=>'success','data'=>$res,'msg' => 'found records'));

                } else {
                    http_response_code(200);
                    echo json_encode(array('status'=>'error', 'data'=>array(), 'msg' => 'no result found',));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing Session ID Parameter in request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'GET', '/v1/api/fetch-game-result-details/[*:action]', function($session_id) {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = isset($session_id)?$session_id:"";

            if (!empty($session_id)) {
                $q1 = $db->select("select gr.*,gs.gms_type,oc.occ_name,oc.occ_age,oc.occ_gender from tbl_game_results gr 
                                                inner join tbl_game_sessions gs on gs.gms_id = gr.gr_gms_id 
                                                inner join tbl_occupants oc on oc.occ_id = gr.gr_occ_id 
                                                where gr.gr_gms_id='".$session_id."' ");
                if($q1 > 0) {
                    $ls_1 = !empty($q1[0]['ls_1_ans'])?$q1[0]['ls_1_ans']:0;
                    $ls_2 = !empty($q1[0]['ls_2_ans'])?$q1[0]['ls_2_ans']:0;
                    $ls_3 = !empty($q1[0]['ls_3_ans'])?$q1[0]['ls_3_ans']:0;

                    $ws_1 = !empty($q1[0]['ws_1_ans'])?$q1[0]['ws_1_ans']:0;
                    $ws_2 = !empty($q1[0]['ws_2_ans'])?$q1[0]['ws_2_ans']:0;
                    $ws_3 = !empty($q1[0]['ws_3_ans'])?$q1[0]['ws_3_ans']:0;
                    $ws_4 = !empty($q1[0]['ws_4_ans'])?$q1[0]['ws_4_ans']:0;

                    $ps_1 = !empty($q1[0]['ps_1_ans'])?$q1[0]['ps_1_ans']:0;
                    $ps_2 = !empty($q1[0]['ps_2_ans'])?$q1[0]['ps_2_ans']:0;
                    $ps_3 = !empty($q1[0]['ps_3_ans'])?$q1[0]['ps_3_ans']:0;
                    $ps_4 = !empty($q1[0]['ps_4_ans'])?$q1[0]['ps_4_ans']:0;

                    $ss_1 = !empty($q1[0]['ss_1_ans'])?$q1[0]['ss_1_ans']:0;

                    $total = $ls_1 + $ls_2 + $ls_3 + $ws_1 + $ws_2 + $ws_3 + $ws_4 + $ps_1 + $ps_2 + $ps_3 + $ps_4 + $ss_1;
                    $percen = ($total/25)*100;
                    $format_percent = number_format($percen,2)."%";
                    $status = ($total == 100)?'Completed':'Incomplete';

                    $res = array(
                        "session_id" => $q1[0]['gr_gms_id'],
                        "occ_id" => $q1[0]['gr_occ_id'],
                        "occ_name" => $q1[0]['occ_name'],
                        "occ_age" => $q1[0]['occ_age'],
                        "occ_gender" => $q1[0]['occ_gender'],
                        "gms_type" => $q1[0]['gms_type'],
                        "letter_stage_1" => $ls_1,
                        "letter_stage_2" => $ls_2,
                        "letter_stage_3" => $ls_3,
                        "word_stage_1" => $ws_1,
                        "word_stage_2" => $ws_2,
                        "word_stage_3" => $ws_3,
                        "word_stage_4" => $ws_4,
                        "paragraph_stage_1" => $ps_1,
                        "paragraph_stage_2" => $ps_2,
                        "paragraph_stage_3" => $ps_3,
                        "paragraph_stage_4" => $ps_4,
                        "story_stage_1" => $ss_1,
                        "total_score" => $total,
                        "score_percent" => $format_percent,
                        "status" => $status
                    );

                    http_response_code(200);
                    echo json_encode(array('status'=>'success','data'=>$res,'msg' => 'found records'));

                } else {
                    http_response_code(200);
                    echo json_encode(array('status'=>'error', 'data'=>array(), 'msg' => 'no result found',));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing Session ID Parameter in request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'POST', '/v1/api/delete-game-result', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = $db->CleanDBData($data->session_id);
            $array_where = array('gms_id' => $db->CleanDBData($session_id), 'usr_id' => $db->CleanDBData($usr_id));
            $Qry = $db->Delete('tbl_game_sessions',$array_where);
            if($Qry) {
                $array_where2 = array('gr_gms_id' => $db->CleanDBData($session_id), 'gr_usr_id' => $db->CleanDBData($usr_id));
                $db->Delete('tbl_game_results',$array_where2);

                http_response_code(200);
                echo json_encode(array('status'=>'success','msg' => 'Result deleted successfully'));
            } else {
                http_response_code(400);
                echo json_encode(array('status'=>'error', 'msg' => 'unable to delete game result',));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status" => 0, "error" => $ex->getMessage(), "message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'msg' => 'Unauthorized dev! missing/invalid developer key'));
    }
});

$router->map( 'POST', '/v1/api/submit-letter-stage-2', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = isset($data->session_id)?$data->session_id:"";
            $answer = isset($data->answer)?$data->answer:"";
            $data_obj = isset($data->data)?json_encode($data->data):"";

//            print_r(json_encode($data_obj)); die();
            if (!empty($session_id) && !empty($session_id)) {
                if ($data_obj !="") {
                    $res_arr = array('ls_2_ans'=>$answer,'ls_2_data'=>$data_obj);
                    $q0 = $db->Update('tbl_game_results',$res_arr,array('gr_gms_id'=>$session_id,'gr_usr_id'=>$usr_id));
                    if ($q0 > 0) {
                        http_response_code(200);
                        echo json_encode(array(
                            "status"=>"success","session_id"=>$session_id,
                            "message" => "Letter Level stage Two (2) saved successfully"
                        ));
                    } else {
                        http_response_code(400);
                        echo json_encode(array("status"=>'error',"message" => "Unable to save result, pls try again."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("status"=>'error',"message" => "Results object cannot be empty."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing/Empty Session ID Parameter in body request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});

$router->map( 'POST', '/v1/api/submit-letter-stage-3', function() {
    $data = json_decode(file_get_contents("php://input"));
    $db = app_db();
    $dev_key = getDeveloperKey();
    $dev_key_res = $db->select("select * from tbl_developer_keys where access_code='".$dev_key."' ");
    if (!empty($dev_key) && ($dev_key_res)) {
        $token = getBearerToken();
        try {
            $secret_key = getenv('HTTP_MY_SECRET');
            $decoded_data = JWT::decode($token, $secret_key, array('HS512'));
            $usr_id = $decoded_data->usr_id;

            $session_id = isset($data->session_id)?$data->session_id:"";
            $answer = isset($data->answer)?$data->answer:"";
            $data_obj = isset($data->data)?json_encode($data->data):"";

//            print_r(json_encode($data_obj)); die();
            if (!empty($session_id) && !empty($session_id)) {
                if ($data_obj !="") {
                    $res_arr = array('ls_3_ans'=>$answer,'ls_3_data'=>$data_obj);
                    $q0 = $db->Update('tbl_game_results',$res_arr,array('gr_gms_id'=>$session_id,'gr_usr_id'=>$usr_id));
                    if ($q0 > 0) {
                        http_response_code(200);
                        echo json_encode(array(
                            "status"=>"success","session_id"=>$session_id,
                            "message" => "Letter Level stage Three (3) saved successfully"
                        ));
                    } else {
                        http_response_code(400);
                        echo json_encode(array("status"=>'error',"message" => "Unable to save result, pls try again."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("status"=>'error',"message" => "Results object cannot be empty."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("status"=>'error',"message" => "Missing/Empty Session ID Parameter in body request."));
            }
        } catch (Exception $ex) {
            http_response_code(400);
            echo json_encode(array("status"=>'error', "error" => $ex->getMessage(),"message" => "Invalid token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 'error',"message" => "Unauthorized dev! missing/invalid developer key"));
    }
});



?>