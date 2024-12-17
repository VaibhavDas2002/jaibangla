<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Users_audit_trail extends Model 
{
  
    protected $table = 'users_audit_trail';
    protected $primaryKey='id';
    protected $guarded = [];
}
 