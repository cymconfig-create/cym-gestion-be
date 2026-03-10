<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Action
 * 
 * @property int $action_id
 * @property string $code
 * @property string $name
 * @property int $profile_id
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Profile[] $profiles
 *
 * @package App\Models
 */
class Action extends Model
{
	protected $table = 'actions';
	protected $primaryKey = 'action_id';

	protected $casts = [
		'profile_id' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'profile_id',
		'status'
	];

	public function profiles()
	{
		return $this->belongsToMany(Profile::class, 'action_profiles')
					->withPivot('action_profile_id', 'status')
					->withTimestamps();
	}
}
