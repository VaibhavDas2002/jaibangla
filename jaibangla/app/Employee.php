<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    protected $primaryKey='id';


    public function Departments()
    {
        
        return $this->belongsTo('App\Department','department_id');
    }
}
