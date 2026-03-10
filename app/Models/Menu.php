<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 * 
 * @property int $menu_id
 * @property string $code
 * @property string $name
 * @property string $route
 * @property int $position
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Profile[] $profiles
 * @property Collection|SubMenu[] $sub_menus
 *
 * @package App\Models
 */
class Menu extends Model
{
	protected $table = 'menus';
	protected $primaryKey = 'menu_id';

	protected $casts = [
		'position' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'route',
		'position',
		'status'
	];

	public function profiles()
	{
		return $this->belongsToMany(Profile::class, 'menu_profiles', 'menu_id', 'profile_id');
	}

	public function sub_menus()
	{
        return $this->hasMany(SubMenu::class, 'menu_id', 'menu_id');
	}
}
