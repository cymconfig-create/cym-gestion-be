<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Selector
 * * @property int $selector_id
 * @property string|null $code
 * @property string $name
 * @property int|null $order
 * @property string|null $dad_selector_code
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Selector extends Model
{
	protected $table = 'selectors';
	protected $primaryKey = 'selector_id';

	protected $casts = [
		'status' => 'bool',
		'order' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
		'order',
		'dad_selector_code',
		'status'
	];
}
