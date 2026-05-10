<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * @property int $employee_id
 * @property string|null $photography_route
 * @property string $full_name
 * @property int $selector_identification
 * @property string $identification_number
 * @property string|null $place_of_issue
 * @property string|null $residence_address
 * @property string|null $complement_address
 * @property string|null $city_address
 * @property string|null $department_address
 * @property Carbon|null $birthdate
 * @property string|null $place_of_birth
 * @property string|null $email
 * @property string|null $phone_number
 * @property string|null $cell_phone_number
 * @property int|null $selector_academic_level
 * @property int|null $selector_arl
 * @property int|null $selector_eps
 * @property int|null $selector_pension_fund
 * @property int|null $selector_severance_fund
 * @property int|null $selector_type_of_contract
 * @property string|null $job_position
 * @property Carbon|null $contract_date
 * @property int|null $selector_blood_type
 * @property string|null $allergies
 * @property int|null $selector_civil_status
 * @property int|null $selector_identification_contact
 * @property string|null $identification_number_contact
 * @property string|null $full_name_contact
 * @property string|null $phone_number_contact
 * @property string|null $cell_phone_number_contact
 * @property string|null $email_contact
 * @property bool $status
 * @property int|null $company_id
 * @property int|null $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Company|null $company
 * @property Collection|Company[] $companies
 * @property Collection|Attachment[] $attachments
 * @property User|null $user
 *
 * @package App\Models
 */
class Employee extends Model
{
	protected $table = 'employees';
	protected $primaryKey = 'employee_id';

	protected $casts = [
		'selector_identification' => 'int',
		'birthdate' => 'datetime',
		'selector_academic_level' => 'int',
		'selector_arl' => 'int',
		'selector_eps' => 'int',
		'selector_pension_fund' => 'int',
		'selector_severance_fund' => 'int',
		'selector_type_of_contract' => 'int',
		'contract_date' => 'datetime',
		'selector_blood_type' => 'int',
		'selector_civil_status' => 'int',
		'selector_identification_contact' => 'int',
		'status' => 'bool',
		'company_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'full_name',
		'selector_identification',
		'identification_number',
		'place_of_issue',
		'residence_address',
		'complement_address',
		'city_address',
		'department_address',
		'birthdate',
		'place_of_birth',
		'email',
		'phone_number',
		'cell_phone_number',
		'selector_academic_level',
		'selector_arl',
		'selector_eps',
		'selector_pension_fund',
		'selector_severance_fund',
		'selector_type_of_contract',
		'job_position',
		'contract_date',
		'selector_blood_type',
		'allergies',
		'selector_civil_status',
		'selector_identification_contact',
		'identification_number_contact',
		'full_name_contact',
		'phone_number_contact',
		'cell_phone_number_contact',
		'email_contact',
		'status',
		'company_id',
		'user_id'
	];

	public function getRulesCreate()
	{
		return [
			'full_name' => 'required|string',
			'selector_identification' => 'required|int',
			'identification_number' => 'required|string',
			'user_id' => 'nullable|int',
			'email' => 'required|email|string'
		];
	}

	public function company()
	{
		return $this->belongsTo(Company::class, 'company_id', 'company_id');
	}

	public function companies()
	{
		return $this->hasMany(Company::class, 'system_manager_id', 'employee_id');
	}

	public function attachments()
	{
		return $this->hasMany(Attachment::class, 'employee_id', 'employee_id');
	}

	public function users()
	{
		return $this->belongsTo(User::class, 'user_id', 'user_id');
	}

	/**
	 * Obtiene el tipo de identificación del empleado.
	 */
	public function identificationType()
	{
		return $this->belongsTo(Selector::class, 'selector_identification', 'selector_id');
	}

	/**
	 * Obtiene el nivel académico del empleado.
	 */
	public function academicLevel()
	{
		return $this->belongsTo(Selector::class, 'selector_academic_level', 'selector_id');
	}

	/**
	 * Obtiene el tipo de ARL del empleado.
	 */
	public function arlType()
	{
		return $this->belongsTo(Selector::class, 'selector_arl', 'selector_id');
	}

	/**
	 * Obtiene el tipo de EPS del empleado.
	 */
	public function epsType()
	{
		return $this->belongsTo(Selector::class, 'selector_eps', 'selector_id');
	}

	/**
	 * Obtiene el tipo de Fondo de Pensión del empleado.
	 */
	public function pensionFundType()
	{
		return $this->belongsTo(Selector::class, 'selector_pension_fund', 'selector_id');
	}

	/**
	 * Obtiene el tipo de Fondo de Cesantías del empleado.
	 */
	public function severanceFundType()
	{
		return $this->belongsTo(Selector::class, 'selector_severance_fund', 'selector_id');
	}

	/**
	 * Obtiene el tipo de Contrato del empleado.
	 */
	public function contractType()
	{
		return $this->belongsTo(Selector::class, 'selector_type_of_contract', 'selector_id');
	}

	/**
	 * Obtiene el tipo de Sangre del empleado.
	 */
	public function bloodType()
	{
		return $this->belongsTo(Selector::class, 'selector_blood_type', 'selector_id');
	}

	/**
	 * Obtiene el Estado Civil del empleado.
	 */
	public function civilStatus()
	{
		return $this->belongsTo(Selector::class, 'selector_civil_status', 'selector_id');
	}

	/**
	 * Obtiene el tipo de identificación del Contacto de Emergencia.
	 */
	public function contactIdentificationType()
	{
		return $this->belongsTo(Selector::class, 'selector_identification_contact', 'selector_id');
	}
}
