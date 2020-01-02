<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function saveProduct($input)
    {
        $result = self::create($input);
        if ($result) {
            return ['status'=>true, "product"=>$result];
        } else {
            return ["status"=>false];
        }
    }

    public function installation()
    {
        return $this->morphMany('App\Models\Installation', 'model');
    }

    public function getByCode($code)
    {
        return $this->where('code', $code)->first();
    }
}
