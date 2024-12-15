<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\VotingEligibilityImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\VotingEligibility;
use App\Models\Organization;

class VotingEligibilityController extends Component
{
    use WithFileUploads;

    public $file;
    public $org_id;
    public $eligibleVoters;
    public $selectedOrg;
    public $errorMessage;

    public function mount()
    {
        $this->loadEligibleVoters();
    }

    public function loadEligibleVoters()
    {
        if ($this->selectedOrg) {
            $this->eligibleVoters = VotingEligibility::where('org_id', $this->selectedOrg)->get();
        } else {
            $this->eligibleVoters = VotingEligibility::all();
        }
    }

    public function filterByOrganization()
    {
        $this->loadEligibleVoters();
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
            'org_id' => 'required'
        ]);

        try {
            $import = new VotingEligibilityImport($this->org_id);
            Excel::import($import, $this->file);
            
            $duplicatePhones = $import->getDuplicatePhones();
            
            if (count($duplicatePhones) > 0) {
                $this->errorMessage = 'The following phones are already registered: <br>' . 
                    collect($duplicatePhones)->map(function($phone) {
                        return "<span class='badge bg-danger'>{$phone}</span>";
                    })->implode(' ');
            } else {
                session()->flash('success', 'Voter list imported successfully!');
                $this->errorMessage = null;
            }
            
            $this->loadEligibleVoters();
        } catch (\Exception $e) {
            $this->errorMessage = 'Error importing file: ' . $e->getMessage();
        }

        $this->file = null;
    }

    public function render()
    {
        return view('livewire.admin.voting-eligibility', [
            'organizations' => Organization::where('is_active', true)->get()
        ]);
    }
}
