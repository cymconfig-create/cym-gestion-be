<?php

namespace App\Services\Ia;

use App\Models\Attachment;
use App\Models\Document;
use App\Services\Service;
use App\Util\Constants;
use App\Util\DocumentConstants;
use App\Util\DocumentScopeConstants;
use Illuminate\Http\Request;

class DocumentUploadCompletionService extends Service
{
    public function uploadCompletion(Request $request)
    {
        $scope = strtolower((string) $request->query('scope', DocumentScopeConstants::SCOPE_AUTO));
        if (!in_array($scope, [
            DocumentScopeConstants::SCOPE_AUTO,
            DocumentScopeConstants::SCOPE_EMPLOYEE,
            DocumentScopeConstants::SCOPE_COMPANY,
            DocumentScopeConstants::SCOPE_ALL,
        ], true)) {
            return $this->resolve(true, DocumentConstants::INVALID_SCOPE, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        $user = auth()->user();
        $user->loadMissing('profile', 'employee');

        $resolvedScope = $this->resolveAutoScope($scope, $user);
        $hasExplicitCompany = $request->filled('company_id');

        $employeeId = $this->resolveEmployeeId($request, $user);
        $companyId = $hasExplicitCompany ? (int) $request->query('company_id') : null;

        if ($companyId === null
            && $resolvedScope === DocumentScopeConstants::SCOPE_COMPANY
            && $user->employee
            && $user->employee->company_id !== null) {
            $companyId = (int) $user->employee->company_id;
        }

        if ($resolvedScope === DocumentScopeConstants::SCOPE_EMPLOYEE && $employeeId === null) {
            return $this->resolve(true, DocumentConstants::EMPLOYEE_ID_REQUIRED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        if ($resolvedScope === DocumentScopeConstants::SCOPE_COMPANY && $companyId === null) {
            return $this->resolve(true, DocumentConstants::COMPANY_ID_REQUIRED, Constants::NOT_DATA, Constants::CODE_BAD_REQUEST);
        }

        if ($employeeId !== null) {
            $forbidden = $this->forbiddenEmployeeAccess($employeeId, $user);
            if ($forbidden !== null) {
                return $forbidden;
            }
        }
        if ($companyId !== null) {
            $forbidden = $this->forbiddenCompanyAccess($companyId, $user);
            if ($forbidden !== null) {
                return $forbidden;
            }
        }

        $useCompanyAttachments = $companyId !== null
            && ($hasExplicitCompany || $resolvedScope === DocumentScopeConstants::SCOPE_COMPANY);
        $useEmployeeAttachments = !$useCompanyAttachments && $employeeId !== null;

        $codeFilter = $this->codesForScope($resolvedScope);
        if ($resolvedScope === DocumentScopeConstants::SCOPE_ALL) {
            if ($useCompanyAttachments) {
                $codeFilter = DocumentScopeConstants::COMPANY_DOCUMENT_CODES;
            } elseif ($useEmployeeAttachments) {
                $codeFilter = DocumentScopeConstants::EMPLOYEE_DOCUMENT_CODES;
            }
        }

        $requiredQuery = Document::query()->where('status', true);
        if ($codeFilter !== null) {
            $requiredQuery->whereIn('code', $codeFilter);
        }
        $requiredDocs = $requiredQuery->orderBy('document_id')->get();

        if ($requiredDocs->isEmpty()) {
            return $this->resolve(false, DocumentConstants::COMPLETION_SUMMARY, [
                'percentage_total' => 0.0,
                'total_required' => 0,
                'total_uploaded' => 0,
                'scope' => $resolvedScope,
                'documents' => [],
            ], Constants::CODE_SUCCESS);
        }

        $requiredIds = $requiredDocs->pluck('document_id')->all();

        $attachmentQuery = Attachment::query()
            ->whereIn('document_id', $requiredIds);

        if ($useCompanyAttachments) {
            $attachmentQuery->where('company_id', $companyId)->whereNull('employee_id');
        } elseif ($useEmployeeAttachments) {
            $attachmentQuery->where('employee_id', $employeeId);
        } else {
            $attachmentQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->name);
                if ($user->employee_id !== null) {
                    $q->orWhere('employee_id', $user->employee_id);
                }
            });
        }

        $uploadedIds = $attachmentQuery->distinct()->pluck('document_id')->unique()->values()->all();
        $uploadedSet = array_flip($uploadedIds);

        $documentsPayload = [];
        $totalWeightConfigured = 0.0;
        $achievedWeight = 0.0;
        $allHavePercentage = true;

        foreach ($requiredDocs as $doc) {
            $uploaded = isset($uploadedSet[$doc->document_id]);
            $pct = $doc->percentage;
            $hasPct = $pct !== null && $pct !== '';

            if (!$hasPct) {
                $allHavePercentage = false;
            } else {
                $w = (float) $pct;
                $totalWeightConfigured += $w;
                if ($uploaded) {
                    $achievedWeight += $w;
                }
            }

            $documentsPayload[] = [
                'document_id' => $doc->document_id,
                'code' => $doc->code,
                'name' => $doc->name,
                'uploaded' => $uploaded,
                'percentage' => $hasPct ? (float) $pct : null,
            ];
        }

        $totalRequired = $requiredDocs->count();
        $totalUploaded = count($uploadedIds);

        if ($allHavePercentage && $totalWeightConfigured > 0) {
            $percentageTotal = round(($achievedWeight / $totalWeightConfigured) * 100, 2);
        } else {
            $percentageTotal = $totalRequired > 0
                ? round(($totalUploaded / $totalRequired) * 100, 2)
                : 0.0;
        }

        return $this->resolve(false, DocumentConstants::COMPLETION_SUMMARY, [
            'percentage_total' => $percentageTotal,
            'total_required' => $totalRequired,
            'total_uploaded' => $totalUploaded,
            'scope' => $resolvedScope,
            'employee_id' => $employeeId,
            'company_id' => $companyId,
            'documents' => $documentsPayload,
        ], Constants::CODE_SUCCESS);
    }

    private function resolveAutoScope(string $scope, $user): string
    {
        if ($scope !== DocumentScopeConstants::SCOPE_AUTO) {
            return $scope;
        }

        if ($user->employee_id !== null) {
            return DocumentScopeConstants::SCOPE_EMPLOYEE;
        }

        return DocumentScopeConstants::SCOPE_COMPANY;
    }

    private function codesForScope(string $scope): ?array
    {
        return match ($scope) {
            DocumentScopeConstants::SCOPE_EMPLOYEE => DocumentScopeConstants::EMPLOYEE_DOCUMENT_CODES,
            DocumentScopeConstants::SCOPE_COMPANY => DocumentScopeConstants::COMPANY_DOCUMENT_CODES,
            default => null,
        };
    }

    private function resolveEmployeeId(Request $request, $user): ?int
    {
        if ($request->query('employee_id') !== null && $request->query('employee_id') !== '') {
            return (int) $request->query('employee_id');
        }

        return $user->employee_id !== null ? (int) $user->employee_id : null;
    }

    private function forbiddenEmployeeAccess(int $employeeId, $user)
    {
        if ((int) $user->employee_id === $employeeId) {
            return null;
        }

        $code = $user->profile->code ?? '';
        if (in_array($code, ['SUPER', 'ADMIN', 'SGSST', 'GEREN'], true)) {
            return null;
        }

        return $this->resolve(true, DocumentConstants::FORBIDDEN_EMPLOYEE, Constants::NOT_DATA, Constants::CODE_FORBIDDEN);
    }

    private function forbiddenCompanyAccess(int $companyId, $user)
    {
        $code = $user->profile->code ?? '';
        if (in_array($code, ['SUPER', 'ADMIN', 'SGSST', 'GEREN'], true)) {
            return null;
        }

        $employee = $user->employee;
        if ($employee && (int) $employee->company_id === $companyId) {
            return null;
        }

        return $this->resolve(true, DocumentConstants::FORBIDDEN_COMPANY, Constants::NOT_DATA, Constants::CODE_FORBIDDEN);
    }
}
