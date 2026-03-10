<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 * 
 * @property int $company_id
 * @property string $code
 * @property string $nit
 * @property string $name
 * @property int|null $selector_person_type
 * @property int|null $selector_tax_regime
 * @property string|null $route_logo
 * @property string|null $main_address
 * @property string|null $complement_address
 * @property int|null $department_address
 * @property int|null $city_address
 * @property string|null $phone_number
 * @property string|null $cell_phone_number
 * @property string|null $email_sgsst
 * @property string|null $web_site
 * @property string|null $code_ciiu
 * @property int|null $quantity_employees
 * @property int|null $selector_risk_level
 * @property int|null $selector_arl
 * @property int|null $legal_representative_id
 * @property int|null $system_manager_id
 * @property bool $status
 * @property bool $is_eliminated
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Employee|null $employee
 * @property Collection|Attachment[] $attachments
 * @property Collection|Employee[] $employees
 *
 * @package App\Models
 */
class Company extends Model
{
	protected $table = 'companies';
	protected $primaryKey = 'company_id';

	protected $casts = [
		'selector_person_type' => 'int',
		'selector_tax_regime' => 'int',
		'department_address' => 'int',
		'city_address' => 'int',
		'quantity_employees' => 'int',
		'selector_risk_level' => 'int',
		'selector_arl' => 'int',
		'legal_representative_id' => 'int',
		'system_manager_id' => 'int',
		'status' => 'bool',
		'is_eliminated' => 'bool'
	];

	protected $fillable = [
		'code',
		'nit',
		'name',
		'selector_person_type',
		'selector_tax_regime',
		'main_address',
		'complement_address',
		'department_address',
		'city_address',
		'phone_number',
		'cell_phone_number',
		'email_sgsst',
		'web_site',
		'code_ciiu',
		'quantity_employees',
		'selector_risk_level',
		'selector_arl',
		'legal_representative_id',
		'system_manager_id',
		'status',
		'is_eliminated'
	];

	public function getRulesCreate()
	{
		return [
			'nit' => 'required|string|not_in:null',
			'name' => 'required|string|not_in:null',
			'email' => 'required|email|string'
		];
	}

	public function attachments()
	{
		return $this->hasMany(Attachment::class, 'company_id', 'company_id');
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'company_id', 'company_id');
	}

	public function systemManager()
	{
		return $this->belongsTo(Employee::class, 'system_manager_id', 'employee_id');
	}

	public function legalRepresentative()
	{
		return $this->belongsTo(Employee::class, 'legal_representative_id', 'employee_id');
	}

	/**
	 * Obtiene el selector del tipo de persona.
	 */
	public function personType()
	{
		return $this->belongsTo(Selector::class, 'selector_person_type', 'selector_id');
	}

	/**
	 * Obtiene el selector del régimen fiscal.
	 */
	public function taxRegime()
	{
		return $this->belongsTo(Selector::class, 'selector_tax_regime', 'selector_id');
	}

	/**
	 * Obtiene el selector del nivel de riesgo.
	 */
	public function riskLevel()
	{
		return $this->belongsTo(Selector::class, 'selector_risk_level', 'selector_id');
	}

	/**
	 * Obtiene el selector de la ARL.
	 */
	public function arl()
	{
		return $this->belongsTo(Selector::class, 'selector_arl', 'selector_id');
	}
}
