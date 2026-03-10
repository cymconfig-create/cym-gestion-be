<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ResourcesProfile
 * 
 * @property int $id
 * @property int $id_resource
 * @property int $id_profile
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Resource $resource
 * @property Profile $profile
 *
 * @package App\Models
 */
class ResourcesProfile extends Model
{
	protected $table = 'resources_profiles';

	protected $casts = [
		'id_resource' => 'int',
		'id_profile' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'id_resource',
		'id_profile',
		'status'
	];

	public function resource()
	{
		return $this->belongsTo(Resource::class, 'id_resource');
	}

	public function profile()
	{
		return $this->belongsTo(Profile::class, 'id_profile');
	}
}
