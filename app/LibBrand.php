<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibBrand extends Model
{
    protected $connection = 'proc_login';

    protected $table = 'lib_brand';

    protected $primaryKey = 'brand_id';

    public $timestamps = false;
}
