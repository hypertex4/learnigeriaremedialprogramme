<?php
global $api;

class GlobalApi {
    public function curlQueryPost($url, $post_data) {

        $fields_string = http_build_query($post_data);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_test_a02d26da16d6e145bd6e8bf6d665a0db4ea91a09",
            "Cache-Control: no-cache",
        ));
        $output = curl_exec($ch);
        if ($output === FALSE) {
            return "cURL Error: " . curl_error($ch);
        }
        curl_close($ch);

        $arr = json_decode($output);
        return $arr;
    }

    public function curlQueryGet($url) {
       $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_a02d26da16d6e145bd6e8bf6d665a0db4ea91a09",
            "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $arr = json_decode($response);
        $arrErr = json_decode($err);


        if ($err) {
            return $arrErr;
        } else {
            return $arr;
        }    
    }
}


$api = new GlobalApi();
?>