<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Domain
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $id_father
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Domain|null $domain
 * @property Collection|Company[] $companies
 * @property Collection|Domain[] $domains
 * @property Collection|Employee[] $employees
 * @property Employee $employee
 *
 * @package App\Models
 */
class Domain extends Model
{
	protected $table = 'domains';

	protected $casts = [
		'id_father' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'id_father',
		'status'
	];

	public function domain()
	{
		return $this->belongsTo(Domain::class, 'id_father');
	}

	public function domains()
	{
		return $this->hasMany(Domain::class, 'id_father');
	}
}
