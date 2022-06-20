<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblActiveScheme extends Model
{
    protected $table = 'tbl_scheme';

    protected $primaryKey = 'scheme_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
