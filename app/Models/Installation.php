<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $guarded = [];

    public function getInstallationByNamePurchaseCode($user_name, $purchase_code)
    {
        return $this->where('user_name', $user_name)->where('purchase_code', $purchase_code)->get();
    }

    public function getInstallNo($user_name, $purchase_code)
    {
        $installs = $this->getInstallationByNamePurchaseCode($user_name, $purchase_code);
        $install_no = 1;
        if ($installs->count() > 0) {
            $install_no += $installs->count();
        }
        return $install_no;
    }

    public function createInstall($input)
    {
       
        $productObj = new Product();
        $product =  $productObj->getByCode($input['product_id']);
        unset($input['product_id']);
        $install = new Installation($input);
        $result = $product->installation()->save($install);
        if ($result) {
            return ['status'=>true, "install"=>$result];
        } else {
            return ["status"=>false];
        }
    }
}
