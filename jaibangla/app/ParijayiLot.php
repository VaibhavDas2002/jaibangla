<?php
namespace App;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ParijayiLot extends Model
{
    protected $connection = 'pgsql_sp';
    protected $table = 'lot_master';
    protected $primaryKey='id';
    protected $fillable = array('lot_created_by','lot_no','ben_count','file_name','lot_type');

    // public function getCreatedAtAttribute($value)
    // {
    //     $createdAt = Carbon::parse($value);
    //     return $createdAt->format('M d Y');
    // }
}