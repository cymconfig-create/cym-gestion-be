<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * 
 * @property int $id
 * @property int $id_company
 * @property int $id_type_file
 * @property int|null $id_employee
 * @property string $url
 * @property int $status
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Action $action
 * @property TypeFile $type_file
 * @property Company $company
 *
 * @package App\Models
 */
class File extends Model
{
	protected $table = 'files';

	protected $casts = [
		'id_company' => 'int',
		'id_type_file' => 'int',
		'id_employee' => 'int',
		'status' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'id_company',
		'id_type_file',
		'url',
		'id_employee',
		'created_by',
		'status'
	];

	public $rulesCreate = [
		'id_company' => 'required|integer',
		'id_type_file' => 'required|integer',
		'created_by' => 'required|integer',
		'file' => 'required|mimes:pdf|max:1024'
	];

	public function typeFile()
	{
		return $this->belongsTo(TypeFile::class, 'id_type_file');
	}

	public function usuario()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function company()
	{
		return $this->belongsTo(Company::class, 'id_company');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'id_employee');
	}
}
