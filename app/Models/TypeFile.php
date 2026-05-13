<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeFile
 * 
 * @property int $id
 * @property int|null $id_company
 * @property string $name
 * @property float|null $percentage
 * @property string|null $description
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Company|null $company
 * @property Collection|File[] $files
 *
 * @package App\Models
 */
class TypeFile extends Model
{
	protected $table = 'type_files';

	protected $casts = [
		'id_company' => 'int',
		'percentage' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'id_company',
		'name',
		'percentage',
		'description',
		'status'
	];

	public function companies()
	{
		return $this->belongsToMany(Company::class, 'files', 'id_file', 'id_company')
					->withPivot('id', 'file', 'status')
					->withTimestamps();
	}

	public function employees()
	{
		return $this->belongsToMany(Employee::class, 'files', 'id_file', 'id_employee')
					->withPivot('id', 'file', 'id_company', 'status')
					->withTimestamps();
	}
}
