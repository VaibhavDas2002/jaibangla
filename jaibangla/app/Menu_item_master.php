<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Menu_item_master extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'm_menu_item_master';
    protected $primaryKey='id';
    protected $guarded = [];
    public function parentMenu()
    {
        return $this->hasMany(Menu_item_master::class, 'parent_id');
    }
 
    // This is method where we implement recursive relationship
    public function childMenu()
    {
        return $this->hasMany(Menu_item_master::class, 'parent_id')->with('parentMenu');
    }
}
 