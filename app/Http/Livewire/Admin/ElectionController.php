<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Election;
use App\Models\Organization;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;


class ElectionController extends Component
{
    public $election_topic, $description, $start_date, $end_date, $status,$type, $nominate_period_start, $nominate_period_end;
    public $election_id;
    public $isOpen = false;
    public $isView = false;
    public $confirmingDeletion = false;
    public $org_id;
    public $position = [];

    protected $rules = [
        'election_topic' => 'required|min:3',
        'type' => 'required',
        'position' => 'required|array',
        'description' => 'required|min:10',
        'nominate_period_start' => 'required|date',
        'nominate_period_end' => 'required|date|after_or_equal:nominate_period_start',
        'start_date' => 'required|date|after_or_equal:nominate_period_end',
        'end_date' => 'required|date|after_or_equal:start_date',
        'status' => 'required',
        'org_id' => 'required'
    ];

    protected $messages = [
        'election_topic.required' => 'The election topic is required.',
        'type.required' => 'The election type is required.',
        'position.required' => 'The position is required.',
        'description.required' => 'The description is required.',
        'nominate_period_start.required' => 'The nomination period start is required.',
        'nominate_period_start.before_or_equal' => 'The nomination period start must be before or equal to the nomination period end.',
        'nominate_period_end.required' => 'The nomination period end is required.',
        'nominate_period_end.after_or_equal' => 'The nomination period end must be after or equal to the nomination period start.',
        'start_date.required' => 'The start date is required.',
        'start_date.after_or_equal' => 'The start date must be after or equal to the nomination period end.',
        'end_date.required' => 'The end date is required.',
        'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        'status.required' => 'The status is required.'
    ];

    public function mount()
    {
        $this->position = [];
    }

    public function render()
    {
        $elections = Election::with('organization')->get();
        return view('livewire.admin.election', [
            'elections' => $elections,
            'organizations' => Organization::orderBy('org_name')->get()
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'election_topic' => 'required|min:3',
            'type' => 'required',
            'position' => 'required|array',
            'description' => 'required|min:10',
            'nominate_period_start' => 'required|date',
            'nominate_period_end' => 'required|date|after_or_equal:nominate_period_start',
            'start_date' => 'required|date|after_or_equal:nominate_period_end',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required',
            'org_id' => 'required'
        ]);

        Election::create([
            'election_topic' => $this->election_topic,
            'type' => $this->type,
            'position' => implode(',', $this->position),
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'nominate_period_start' => $this->nominate_period_start,
            'nominate_period_end' => $this->nominate_period_end,
            'status' => $this->status,
            'org_id' => $this->org_id,
            'result_release_date' => '2025-01-08' 
        ]);

        session()->flash('success', 'Election Created Successfully.');
        $this->dispatch('alert-shown');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $election = Election::with('organization')->findOrFail($id);
        
        $this->resetInputFields();
        
        $this->election_id = $election->election_id;
        $this->election_topic = $election->election_topic;
        $this->type = $election->type;
        
        $this->position = explode(',', $election->position);
        
        $this->description = $election->description;
        $this->nominate_period_start = date('Y-m-d', strtotime($election->nominate_period_start));
        $this->nominate_period_end = date('Y-m-d', strtotime($election->nominate_period_end));
        $this->start_date = date('Y-m-d', strtotime($election->start_date));
        $this->end_date = date('Y-m-d', strtotime($election->end_date));
        $this->status = $election->status;
        $this->org_id = $election->org_id;
        
        \Log::info('Election type:', ['type' => $this->type]);
        
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'election_topic' => 'required|min:3',
            'type' => 'required',
            'position' => 'required|array',
            'description' => 'required|min:10',
            'nominate_period_start' => 'required|date',
            'nominate_period_end' => 'required|date|after_or_equal:nominate_period_start',
            'start_date' => 'required|date|after_or_equal:nominate_period_end',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required',
            'org_id' => 'required'
        ]);

        Election::where('election_id', $this->election_id)->update([
            'election_topic' => $this->election_topic,
            'type' => $this->type,
            'position' => implode(',', $this->position),
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'nominate_period_start' => $this->nominate_period_start,
            'nominate_period_end' => $this->nominate_period_end,
            'status' => $this->status,
            'org_id' => $this->org_id
        ]);

        session()->flash('success', 'Election Updated Successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function view($id)
    {
        $election = Election::findOrFail($id);
        
        // Reset any previous state
        $this->resetInputFields();
        
        $this->isView = true;
        
        $this->election_id = $election->election_id;
        $this->election_topic = $election->election_topic;
        $this->type = $election->type;
        
        // Decode the position from JSON
        $this->position = json_decode($election->position, true) ?? [];
        
        $this->description = $election->description;
        $this->nominate_period_start = date('Y-m-d', strtotime($election->nominate_period_start));
        $this->nominate_period_end = date('Y-m-d', strtotime($election->nominate_period_end));
        $this->start_date = date('Y-m-d', strtotime($election->start_date));
        $this->end_date = date('Y-m-d', strtotime($election->end_date));
        $this->status = $election->status;
        $this->org_id = $election->org_id;

        $this->openModal();
    }

    public function confirmDelete($election_id)
    {
        $this->election_id = $election_id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        Election::find($this->election_id)->delete();
        session()->flash('success', 'Election Deleted Successfully.');
        $this->confirmingDeletion = false;
    }

    private function resetInputFields()
    {
        $this->election_topic = '';
        $this->type = '';
        $this->position = [];
        $this->description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->nominate_period_start = '';
        $this->nominate_period_end = '';
        $this->result_release_date = '';
        $this->status = '';
        $this->election_id = null;
        $this->isView = false;
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->dispatch('modal-open');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->dispatch('modal-close');
    }

    public function updatedNominatePeriodStart($value)
    {
        if (!empty($value)) {
            $this->start_date = $value;
        }
    }

    public function updatedNominatePeriodEnd($value)
    {
        if (!empty($value)) {
            $this->end_date = $value;
        }
    }

    public function getAllElections(){
        $elections = Election::all();
        return response()->json($elections);
    }

    public function getElectionInfo($id){
        $election = Election::find($id);
        return response()->json([
            'election_topic' => $election->election_topic,
            'type' => $election->type,
            'position' => $election->position,
            'description' => $election->description,
            'start_date' => $election->start_date,
            'end_date' => $election->end_date,
            'nominate_period_start' => $election->nominate_period_start,
            'nominate_period_end' => $election->nominate_period_end,
            'result_release_date' => $election->result_release_date,
            'status' => $election->status,
            'org_id' => $election->org_id
        ]);
    }
}
