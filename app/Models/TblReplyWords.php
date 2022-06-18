<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblReplyWords extends Model
{
    protected $table = 'tbl_wa_word';

    protected $primaryKey = 'word_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
