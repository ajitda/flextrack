<?php
namespace App\Http\Controllers\Traits;

use GuzzleHttp\Client;

trait CommonTrait
{
    public function getMac()
    {
        exec("ipconfig /all", $output);
        foreach($output as $line){
            if (preg_match("/(.*)Physical Address(.*)/", $line)){
            $mac = $line;
            $mac = str_replace("Physical Address. . . . . . . . . :","",$mac);
            }
        }
        
        return trim($mac);
    }

    public function getClientIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function checkPurchase($purchase_code)
    {
        $client = new Client();
        $url = "https://api.envato.com/v3/market/author/sale?code=".$purchase_code;
        $res = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer fOXk6KmMtINX9Xm4NzXrwdD2NcJdia1r'
            ]
        ]);
        return $res;
    }
}