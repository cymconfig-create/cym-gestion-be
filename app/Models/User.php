<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * 
 * @property int $user_id
 * @property string $name
 * @property string $password
 * @property int $profile_id
 * @property int|null $employee_id
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Employee|null $employee
 * @property Profile $profile
 *
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject
{
	use HasApiTokens, HasFactory, Notifiable;

	protected $table = 'users';
	protected $primaryKey = 'user_id';

	protected $casts = [
		'profile_id' => 'int',
		'employee_id' => 'int',
		'status' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'name',
		'password',
		'profile_id',
		'employee_id',
		'status'
	];

	public function getRulesCreate()
	{
		return [
			'profile_id' => 'required|integer',
			'name' => 'required|string|unique:users,name,' . $this->user_id . ',user_id',
			'password' => 'required|string'
		];
	}

	public function employee()
	{
		return $this->hasOne(Employee::class, 'user_id', 'user_id');
	}

	public function profile()
	{
		return $this->belongsTo(Profile::class, 'profile_id', 'profile_id');
	}

	/**
	 * Obtiene una lista de usuarios filtrada según el perfil del usuario autenticado.
	 * Si el usuario logueado tiene perfil 'SUPER', lista todos los usuarios.
	 * De lo contrario, excluye a los usuarios con perfiles 'SUPER' y 'ADMIN'.
	 *
	 */
	public static function getFilteredUsers()
	{
		$loggedInUser = auth()->user();
		// Si no hay un usuario autenticado, o su code perfil es 'SUPER', se listan todos los usuarios.
		if (!$loggedInUser || ($loggedInUser && $loggedInUser->profile->code == 'SUPER')) {
			return self::all();
		}

		// Si el usuario no es 'SUPER', se excluyen los perfiles con code 'SUPER' y 'ADMIN'.
		return self::whereHas('profile', function ($query) {
			$query->whereNotIn('code', ['SUPER', 'ADMIN']);
		})->get();
	}

	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	public function getJWTCustomClaims()
	{
		return [];
	}
}
