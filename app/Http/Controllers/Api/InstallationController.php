<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstallationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function checkInstallation(Request $request)
    {
        $inputdata = $request->all();
        $validation = $this->validateInstallation($inputdata);
        if ($validation->fails()) {
            return $this->sendError('Validation Error', $validation->errors());
        }
        try {
            $res = $this->checkPurchase($inputdata['purchase_code']);
            if ($res->getStatusCode() != 200){
                return $this->sendError('Invalid purchase code');
            }
        } catch(\Exception $e) {
            $response = $e->getResponse();
            if($response->getStatusCode() != 504 ){
                return $this->sendError('Invalid purchase code');
            }
        }
        
        $inputdata['ip'] = $this->getClientIp();
        $installObj = new Installation();
        $install = $installObj->getInstallationByNamePurchaseCode($inputdata['user_name'], $inputdata['purchase_code']);
        if (!empty($install->expired)) {
            return $this->sendError('Installation Expired');
        }
        $install_num = $installObj->getInstallNo($inputdata['user_name'], $inputdata['purchase_code']);
        if ($install_num > 3) {
            return $this->sendError('Max Installation Reached, Please contact support.');
        }
        $inputdata['install_num'] = $install_num;
        $inputdata['verification_token'] = $this->generateVerificationToken($inputdata['user_name'].":".$inputdata['purchase_code']);
        $result = $installObj->createInstall($inputdata);
        if ($result['status']) {
            return  $this->sendResponse($inputdata['verification_token'], 'Installation Successfull!');
        }
    }

    private function validateInstallation(array $data)
    {
        return Validator::make($data,[
            'user_name' => ['required', 'string', 'max:255'],
            'product_id' => ['required', 'string', 'exists:products,"code"'],
            'purchase_code'=>['required', 'string']
        ]);
    }

    public function generateVerificationToken($data)
    {
        $first_key = base64_decode('Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');
        $second_key = base64_decode('EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');   
        
        $method = "aes-256-cbc";   
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
            
        $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);   
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                
        $output = base64_encode($iv.$second_encrypted.$first_encrypted);   
        return $output;       
    }

    public function checkVerificationToken($input)
    {
        $first_key = base64_decode('Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');
        $second_key = base64_decode('EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');   
        
        $mix = base64_decode($input);
            
        $method = "aes-256-cbc";   
        $iv_length = openssl_cipher_iv_length($method);
                
        $iv = substr($mix,0,$iv_length);
        $second_encrypted = substr($mix,$iv_length,64);
        $first_encrypted = substr($mix,$iv_length+64);
                
        $data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
        
        if (hash_equals($second_encrypted,$second_encrypted_new)){
            return $data;
        }
        
        return false;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
