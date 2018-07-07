<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    // Table Name
    protected $table = 'records';
    //Primary key 
    public $primaryKey = 'id'; 
}
