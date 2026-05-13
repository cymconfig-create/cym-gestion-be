<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Document
 * 
 * @property int $document_id
 * @property string $code
 * @property string $name
 * @property float|null $percentage
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Attachment[] $attachments
 *
 * @package App\Models
 */
class Document extends Model
{
	protected $table = 'documents';
	protected $primaryKey = 'document_id';

	protected $casts = [
		'percentage' => 'float',
		'status' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'percentage',
		'status'
	];

	public function attachments()
	{
		return $this->hasMany(Attachment::class, 'document_id', 'document_id');
	}
}
