<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SubMenu
 * 
 * @property int $sub_menu_id
 * @property string $code
 * @property string $name
 * @property string $route
 * @property int $position
 * @property int $menu_id
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Menu $menu
 *
 * @package App\Models
 */
class SubMenu extends Model
{
	protected $table = 'sub_menus';
	protected $primaryKey = 'sub_menu_id';

	protected $casts = [
		'position' => 'int',
		'menu_id' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'route',
		'position',
		'menu_id',
		'status'
	];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }
}
