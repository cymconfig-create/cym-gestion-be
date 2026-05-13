<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Resource
 * 
 * @property int $id
 * @property string|null $code
 * @property string $name
 * @property string|null $route
 * @property int|null $id_father
 * @property int|null $position
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Resource|null $resource
 * @property Collection|Action[] $actions
 * @property Collection|Resource[] $resources
 * @property Collection|Profile[] $profiles
 *
 * @package App\Models
 */
class Resource extends Model
{
	protected $table = 'resources';

	protected $casts = [
		'id_father' => 'int',
		'position' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
		'route',
		'id_father',
		'position',
		'status'
	];

	public function resource()
	{
		return $this->belongsTo(Resource::class, 'id_father');
	}

	public function profiles()
	{
		return $this->belongsToMany(Profile::class, 'resources_profiles', 'id_resource', 'id_profile')
					->withPivot('id', 'status')
					->withTimestamps();
	}

	public function actions()
	{
		return $this->hasMany(Action::class, 'id_resource');
	}

	public function resources()
	{
		return $this->hasMany(Resource::class, 'id_father');
	}
}
