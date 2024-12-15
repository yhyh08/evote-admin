<?php

namespace App\Imports;

use App\Models\VotingEligibility;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class VotingEligibilityImport implements ToModel, WithHeadingRow, WithValidation
{
    private $org_id;
    private $duplicatePhones = [];

    public function __construct($org_id)
    {
        $this->org_id = $org_id;
    }

    public function model(array $row)
    {
        $existingVoterByPhone = VotingEligibility::where('phone', $row['phone'])
            ->where('org_id', $this->org_id)
            ->first();

        if ($existingVoterByPhone) {
            $this->duplicatePhones[] = $row['phone'];
        }

        if ($existingVoterByPhone) {
            return null;
        }

        return new VotingEligibility([
            'org_id' => $this->org_id,
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ];
    }

    public function getDuplicatePhones()
    {
        return $this->duplicatePhones;
    }
}
