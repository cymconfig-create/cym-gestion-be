<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 * 
 * @property int $id
 * @property string $name
 * @property string $responsible_event
 * @property Carbon|null $event_date
 * @property string $description
 * @property int $id_company
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Company $company
 *
 * @package App\Models
 */
class Event extends Model
{
	protected $table = 'events';

	protected $casts = [
		'event_date' => 'datetime',
		'id_company' => 'int'
	];

	protected $fillable = [
		'name',
		'responsible_event',
		'event_date',
		'description',
		'id_company'
	];

	public function company()
	{
		return $this->belongsTo(Company::class, 'id_company');
	}
}
