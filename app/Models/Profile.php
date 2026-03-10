<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Profile
 * 
 * @property int $profile_id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Action[] $actions
 * @property Collection|Menu[] $menus
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Profile extends Model
{
	protected $table = 'profiles';
	protected $primaryKey = 'profile_id';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'description',
		'status'
	];

	public function actions()
	{
		return $this->belongsToMany(Action::class, 'action_profiles')
			->withPivot('action_profile_id', 'status')
			->withTimestamps();
	}

	public function menus()
	{
		return $this->belongsToMany(Menu::class, 'menu_profiles', 'profile_id', 'menu_id')
			->withPivot('menu_profile_id', 'status')
			->withTimestamps();
	}

	public function users()
	{
		return $this->hasMany(User::class, 'profile_id', 'profile_id');
	}

	/**
	 * Obtiene una lista de perfiles filtrada según el perfil del usuario autenticado.
	 * Si el usuario logueado tiene perfil 'SUPER'.
	 * De lo contrario, excluye a los perfiles con código 'SUPER' y 'ADMIN'.
	 *
	 */
	public static function getFilteredProfiles()
	{
		$loggedInUser = auth()->user();

		// Si no hay un usuario autenticado, o su perfil es 'SUPER', se listan todos los perfiles.
		if ($loggedInUser && $loggedInUser->profile->code == 'SUPER') {
			return self::all();
		}

		// Si el usuario no es 'SUPER', se excluyen los perfiles con código 'SUPER' y 'ADMIN'.
		return self::whereNotIn('code', ['SUPER', 'ADMIN'])->get();
	}
}
