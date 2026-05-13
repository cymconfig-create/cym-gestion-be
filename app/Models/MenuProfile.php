<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuProfile
 * 
 * @property int $menu_profile_id
 * @property int $profile_id
 * @property int $menu_id
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Profile $profile
 * @property Menu $menu
 *
 * @package App\Models
 */
class MenuProfile extends Model
{
	protected $table = 'menu_profiles';
	protected $primaryKey = 'menu_profile_id';

	protected $casts = [
		'profile_id' => 'int',
		'menu_id' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'profile_id',
		'menu_id',
		'status'
	];

	public function profile()
	{
		return $this->belongsTo(Profile::class);
	}

	public function menu()
	{
		return $this->belongsTo(Menu::class);
	}
}
