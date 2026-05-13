<?php

namespace App\Services\Diagnosis;

use App\Repositories\CompanyRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\EventRepository;
use App\Services\Service;
use App\Util\Constants;

class SgsstDiagnosisService extends Service
{
    private const SURVEY_EVENT_PREFIX = 'SGSST_EMPLOYEE_SURVEY';

    private CompanyRepository $companyRepository;
    private EmployeeRepository $employeeRepository;
    private EventRepository $eventRepository;

    public function __construct(
        CompanyRepository $companyRepository,
        EmployeeRepository $employeeRepository,
        EventRepository $eventRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->employeeRepository = $employeeRepository;
        $this->eventRepository = $eventRepository;
    }

    public function companiesOverview()
    {
        [$companies, $employees, $events] = $this->loadBaseData();
        $surveysByEmployee = $this->latestSurveyByEmployee($events);

        $rows = [];
        foreach ($companies as $company) {
            $companyId = (int) ($company->company_id ?? 0);
            $companyEmployees = array_values(array_filter(
                $employees,
                fn ($employee) => (int) ($employee->company_id ?? 0) === $companyId
            ));

            $totalEmployees = count($companyEmployees);
            $surveyed = 0;
            $healthScores = [];
            $psicoScores = [];
            $ergoScores = [];

            foreach ($companyEmployees as $employee) {
                $employeeId = (int) ($employee->employee_id ?? 0);
                if (!isset($surveysByEmployee[$employeeId])) {
                    continue;
                }

                $surveyed++;
                $sections = $surveysByEmployee[$employeeId]['sections'];
                $healthScores[] = (int) ($sections['health'] ?? 0);
                $psicoScores[] = (int) ($sections['intralaboral'] ?? 0);
                $ergoScores[] = (int) ($sections['individual'] ?? 0);
            }

            $coverage = $totalEmployees > 0 ? (int) round(($surveyed / $totalEmployees) * 100) : 0;
            $avgHealth = $this->avg($healthScores);
            $avgPsico = $this->avg($psicoScores);
            $avgErgo = $this->avg($ergoScores);
            $avgAll = $this->avg(array_values(array_filter([$avgHealth, $avgPsico, $avgErgo], fn ($v) => $v > 0)));
            $risk = $surveyed === 0 ? 'Sin datos' : ($avgAll >= 70 ? 'Bajo' : ($avgAll >= 45 ? 'Medio' : 'Alto'));

            $rows[] = [
                'id' => $companyId,
                'name' => (string) ($company->name ?? '—'),
                'nit' => (string) (($company->nit ?? $company->identification_number) ?? '—'),
                'totalEmployees' => $totalEmployees,
                'surveyed' => $surveyed,
                'coverage' => $coverage,
                'avgHealth' => $avgHealth,
                'avgPsico' => $avgPsico,
                'risk' => $risk,
            ];
        }

        return $this->resolve(false, Constants::NOT_MESSAGE, $rows, null);
    }

    public function companyDetail(int $companyId)
    {
        [$companies, $employees, $events] = $this->loadBaseData();
        $company = $this->findCompany($companies, $companyId);
        if (!$company) {
            return $this->resolve(false, Constants::NOT_MESSAGE, null, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $surveysByEmployee = $this->latestSurveyByEmployee($events);
        $companyEmployees = array_values(array_filter(
            $employees,
            fn ($employee) => (int) ($employee->company_id ?? 0) === $companyId
        ));

        $employeeSummary = [];
        $latestSurveyByEmployee = [];
        foreach ($companyEmployees as $employee) {
            $employeeId = (int) ($employee->employee_id ?? 0);
            $survey = $surveysByEmployee[$employeeId]['payload'] ?? null;
            $sections = $survey['sections'] ?? [];
            if ($survey !== null) {
                $latestSurveyByEmployee[$employeeId] = $survey;
            }
            $employeeSummary[] = [
                'id' => $employeeId,
                'name' => (string) ($employee->full_name ?? '—'),
                'cargo' => (string) ($employee->job_position ?? '—'),
                'health' => (int) ($sections['health'] ?? 0),
                'individual' => (int) ($sections['individual'] ?? 0),
                'intralaboral' => (int) ($sections['intralaboral'] ?? 0),
                'extralaboral' => (int) ($sections['extralaboral'] ?? 0),
                'hasData' => $survey !== null,
            ];
        }

        $companyEmployeeIds = array_map(fn ($e) => (int) ($e->employee_id ?? 0), $companyEmployees);
        $recentAlerts = $this->buildRecentAlerts($events, $companyEmployeeIds, $employeeSummary);
        $companyEvents = $this->filterCompanySurveyEvents($events, $companyEmployeeIds);

        $surveyedRows = array_values(array_filter($employeeSummary, fn ($row) => $row['hasData']));
        $avgHealth = $this->avg(array_map(fn ($row) => $row['health'], $surveyedRows));
        $avgErgo = $this->avg(array_map(fn ($row) => $row['individual'], $surveyedRows));
        $avgPsico = $this->avg(array_map(fn ($row) => $row['intralaboral'], $surveyedRows));

        $payload = [
            'company' => [
                'company_id' => (int) ($company->company_id ?? 0),
                'name' => (string) ($company->name ?? '—'),
                'nit' => (string) (($company->nit ?? $company->identification_number) ?? '—'),
            ],
            'totals' => [
                'totalEmployees' => count($companyEmployees),
                'surveyed' => count($surveyedRows),
                'avgHealth' => $avgHealth,
                'avgErgo' => $avgErgo,
                'avgPsico' => $avgPsico,
            ],
            'employeeSummary' => $employeeSummary,
            'recentAlerts' => $recentAlerts,
            'companyEmployees' => array_map(fn ($employee) => (array) $employee, $companyEmployees),
            'companyEvents' => $companyEvents,
            'latestSurveyByEmployee' => $latestSurveyByEmployee,
        ];

        return $this->resolve(false, Constants::NOT_MESSAGE, $payload, null);
    }

    public function employeeDetail(int $employeeId)
    {
        [$companies, $employees, $events] = $this->loadBaseData();
        $employee = $this->findEmployee($employees, $employeeId);
        if (!$employee) {
            return $this->resolve(false, Constants::NOT_MESSAGE, null, Constants::CODE_SUCCESS_NO_CONTENT);
        }

        $company = $this->findCompany($companies, (int) ($employee->company_id ?? 0));
        $employeeEvents = $this->employeeSurveyEvents($events, $employeeId);
        $latestSurvey = $employeeEvents[0]['payload'] ?? null;

        $history = array_map(function ($row) {
            $sections = $row['payload']['sections'] ?? [];
            return [
                'fecha' => $this->dateFromText($row['payload']['submitted_at'] ?? $row['event']->created_at ?? null),
                'tipo' => 'Encuesta SST',
                'health' => (int) ($sections['health'] ?? 0),
                'individual' => (int) ($sections['individual'] ?? 0),
                'intralaboral' => (int) ($sections['intralaboral'] ?? 0),
                'extralaboral' => (int) ($sections['extralaboral'] ?? 0),
            ];
        }, $employeeEvents);

        $payload = [
            'employee' => [
                'employee_id' => (int) ($employee->employee_id ?? 0),
                'full_name' => (string) ($employee->full_name ?? '—'),
                'identification_number' => (string) ($employee->identification_number ?? '—'),
                'job_position' => (string) ($employee->job_position ?? ''),
                'work_area' => (string) ($employee->work_area ?? ''),
            ],
            'companyName' => (string) ($company->name ?? '—'),
            'survey' => $latestSurvey,
            'history' => $history,
            'employeeEvents' => array_map(function ($row) {
                return [
                    'name' => self::SURVEY_EVENT_PREFIX,
                    'description' => json_encode($row['payload']),
                    'created_at' => $this->dateFromText($row['event']->created_at ?? null),
                    'updated_at' => $this->dateFromText($row['event']->updated_at ?? null),
                ];
            }, $employeeEvents),
        ];

        return $this->resolve(false, Constants::NOT_MESSAGE, $payload, null);
    }

    private function loadBaseData(): array
    {
        return [
            $this->companyRepository->all()->all(),
            $this->employeeRepository->all()->all(),
            $this->eventRepository->all()->all(),
        ];
    }

    private function findCompany(array $companies, int $companyId): ?object
    {
        foreach ($companies as $company) {
            if ((int) ($company->company_id ?? 0) === $companyId) {
                return $company;
            }
        }

        return null;
    }

    private function findEmployee(array $employees, int $employeeId): ?object
    {
        foreach ($employees as $employee) {
            if ((int) ($employee->employee_id ?? 0) === $employeeId) {
                return $employee;
            }
        }

        return null;
    }

    private function latestSurveyByEmployee(array $events): array
    {
        $map = [];

        foreach ($events as $event) {
            $name = (string) ($event->name ?? '');
            if (!str_starts_with($name, self::SURVEY_EVENT_PREFIX)) {
                continue;
            }

            $payload = $this->parsePayload($event);
            if (!$payload) {
                continue;
            }

            $employeeId = (int) ($payload['employee_id'] ?? 0);
            if ($employeeId <= 0) {
                continue;
            }

            $currentTs = $this->toTimestamp($payload['submitted_at'] ?? $event->updated_at ?? null);
            $prevTs = isset($map[$employeeId]) ? $map[$employeeId]['timestamp'] : 0;

            if ($currentTs >= $prevTs) {
                $map[$employeeId] = [
                    'timestamp' => $currentTs,
                    'payload' => $payload,
                    'sections' => $payload['sections'] ?? [],
                ];
            }
        }

        return $map;
    }

    private function employeeSurveyEvents(array $events, int $employeeId): array
    {
        $rows = [];
        foreach ($events as $event) {
            $name = (string) ($event->name ?? '');
            if (!str_starts_with($name, self::SURVEY_EVENT_PREFIX)) {
                continue;
            }

            $payload = $this->parsePayload($event);
            if (!$payload || (int) ($payload['employee_id'] ?? 0) !== $employeeId) {
                continue;
            }

            $rows[] = [
                'event' => $event,
                'payload' => $payload,
                'timestamp' => $this->toTimestamp($payload['submitted_at'] ?? $event->updated_at ?? null),
            ];
        }

        usort($rows, fn ($a, $b) => ($b['timestamp'] <=> $a['timestamp']));
        return $rows;
    }

    private function buildRecentAlerts(array $events, array $companyEmployeeIds, array $employeeSummary): array
    {
        $employeeIdsMap = array_fill_keys($companyEmployeeIds, true);
        $nameByEmployee = [];
        foreach ($employeeSummary as $employee) {
            $nameByEmployee[(int) $employee['id']] = $employee['name'];
        }

        $alerts = [];
        foreach ($events as $event) {
            $name = (string) ($event->name ?? '');
            if (!str_starts_with($name, self::SURVEY_EVENT_PREFIX)) {
                continue;
            }

            $payload = $this->parsePayload($event);
            $employeeId = (int) ($payload['employee_id'] ?? 0);
            if (!$payload || !isset($employeeIdsMap[$employeeId])) {
                continue;
            }

            $sections = $payload['sections'] ?? [];
            $avg = (int) round((
                (int) ($sections['health'] ?? 0) +
                (int) ($sections['individual'] ?? 0) +
                (int) ($sections['intralaboral'] ?? 0) +
                (int) ($sections['extralaboral'] ?? 0)
            ) / 4);

            $chipLabel = $avg >= 70 ? 'Favorable' : ($avg >= 45 ? 'En seguimiento' : 'Atención');
            $chipColor = $avg >= 70 ? 'positive' : ($avg >= 45 ? 'warning' : 'negative');

            $alerts[] = [
                'fecha' => $this->dateFromText($payload['submitted_at'] ?? $event->created_at ?? null),
                'nombre' => $nameByEmployee[$employeeId] ?? '—',
                'evento' => 'Encuesta SST completada',
                'chipLabel' => $chipLabel,
                'chipColor' => $chipColor,
                'timestamp' => $this->toTimestamp($payload['submitted_at'] ?? $event->created_at ?? null),
            ];
        }

        usort($alerts, fn ($a, $b) => ($b['timestamp'] <=> $a['timestamp']));

        return array_map(function ($alert) {
            unset($alert['timestamp']);
            return $alert;
        }, array_slice($alerts, 0, 8));
    }

    private function filterCompanySurveyEvents(array $events, array $companyEmployeeIds): array
    {
        $employeeIdsMap = array_fill_keys($companyEmployeeIds, true);
        $rows = [];

        foreach ($events as $event) {
            $name = (string) ($event->name ?? '');
            if (!str_starts_with($name, self::SURVEY_EVENT_PREFIX)) {
                continue;
            }

            $payload = $this->parsePayload($event);
            $employeeId = (int) ($payload['employee_id'] ?? 0);
            if (!$payload || !isset($employeeIdsMap[$employeeId])) {
                continue;
            }

            $rows[] = [
                'name' => $name,
                'description' => json_encode($payload),
                'created_at' => $this->dateFromText($event->created_at ?? null),
                'updated_at' => $this->dateFromText($event->updated_at ?? null),
            ];
        }

        return $rows;
    }

    private function parsePayload(object $event): ?array
    {
        $raw = $event->description ?? null;
        if (!is_string($raw) || trim($raw) === '') {
            return null;
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    private function toTimestamp($value): int
    {
        if ($value === null) {
            return 0;
        }

        if (is_object($value) && method_exists($value, 'toDateTime')) {
            return $value->toDateTime()->getTimestamp();
        }

        $text = (string) $value;
        if ($text === '') {
            return 0;
        }

        $ts = strtotime($text);
        return $ts === false ? 0 : $ts;
    }

    private function dateFromText($value): string
    {
        $ts = $this->toTimestamp($value);
        return $ts > 0 ? date('Y-m-d', $ts) : '';
    }

    private function avg(array $values): int
    {
        if (count($values) === 0) {
            return 0;
        }

        return (int) round(array_sum($values) / count($values));
    }
}
