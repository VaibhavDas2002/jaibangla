<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Menu_designation_mapping extends Model implements Auditable
{
     use \OwenIt\Auditing\Auditable;
     protected $table = 'm_menu_designation_mapping';
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function menu()
    {
        return $this->hasOne('App\Menu_item_master','id','menu_id');
    }
}

