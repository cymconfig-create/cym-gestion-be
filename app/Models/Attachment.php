<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attachment
 * 
 * @property int $attachment_id
 * @property int $document_id
 * @property int $company_id
 * @property int|null $employee_id
 * @property string $route_file
 * @property string|null $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Document $document
 * @property Company $company
 * @property Employee|null $employee
 *
 * @package App\Models
 */
class Attachment extends Model
{
	protected $table = 'attachments';
	protected $primaryKey = 'attachment_id';

	protected $casts = [
		'document_id' => 'int',
		'company_id' => 'int',
		'employee_id' => 'int'
	];

	protected $fillable = [
		'document_id',
		'company_id',
		'employee_id',
		'route_file',
		'created_by'
	];

	public $rulesCreate = [
		'document_id' => 'required|int',
		'company_id' => 'nullable|int',
		'employee_id' => 'nullable|int',
		'route_file' => 'required|file|max:2048|mimes:jpg,png,pdf'
	];

	public function document()
	{
		return $this->belongsTo(Document::class, 'document_id', 'document_id');
	}

	public function company()
	{
		return $this->belongsTo(Company::class, 'company_id', 'company_id');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
	}
}
