<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWAContacts extends Model
{
    protected $table = 'tbl_wa_contact';

    protected $primaryKey = 'cnt_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
