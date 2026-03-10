<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActionProfile
 * 
 * @property int $action_profile_id
 * @property int $profile_id
 * @property int $action_id
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Action $action
 * @property Profile $profile
 *
 * @package App\Models
 */
class ActionProfile extends Model
{
	protected $table = 'action_profiles';
	protected $primaryKey = 'action_profile_id';

	protected $casts = [
		'profile_id' => 'int',
		'action_id' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'profile_id',
		'action_id',
		'status'
	];

	public function action()
	{
		return $this->belongsTo(Action::class);
	}

	public function profile()
	{
		return $this->belongsTo(Profile::class);
	}
}
