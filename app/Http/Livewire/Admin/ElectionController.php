<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Election;

class ElectionController extends Component
{
    public $election_topic, $description, $start_date, $end_date, $status,$type, $position, $nominate_period;
    public $election_id;
    public $isOpen = false;
    public $isView = false;
    public $confirmingDeletion = false;

    protected $rules = [
        'election_topic' => 'required|min:3',
        'type' => 'required',
        'position' => 'required',
        'description' => 'required|min:10',
        'nominate_period' => 'required|date|before_or_equal:start_date',
        'start_date' => 'required|date|after_or_equal:nominate_period',
        'end_date' => 'required|date|after_or_equal:start_date',
        'status' => 'required'
    ];

    protected $messages = [
        'election_topic.required' => 'The election topic is required.',
        'type.required' => 'The election type is required.',
        'position.required' => 'The position is required.',
        'description.required' => 'The description is required.',
        'nominate_period.required' => 'The nomination period is required.',
        'nominate_period.before_or_equal' => 'The nomination period must be before or equal to the start date.',
        'start_date.required' => 'The start date is required.',
        'start_date.after_or_equal' => 'The start date must be after or equal to the nomination period.',
        'end_date.required' => 'The end date is required.',
        'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        'status.required' => 'The status is required.'
    ];

    public function render()
    {
        return view('livewire.admin.election', [
            'elections' => Election::latest()->get()
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        Election::create([
            'election_topic' => $this->election_topic,
            'type' => $this->type,
            'position' => $this->position,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'nominate_period' => $this->nominate_period,
            'status' => $this->status
        ]);

        session()->flash('success', 'Election Created Successfully.');
        $this->dispatch('alert-shown');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $election = Election::findOrFail($id);
        $this->election_id = $election->election_id;
        $this->election_topic = $election->election_topic;
        $this->type = $election->type;
        $this->position = $election->position;
        $this->description = $election->description;
        $this->start_date = $election->start_date;
        $this->end_date = $election->end_date;
        $this->nominate_period = $election->nominate_period;
        $this->status = $election->status;
        
        $this->openModal();
    }

    public function update()
    {
        $this->validate();
      
        Election::where('election_id', $this->election_id)->update([
            'election_topic' => $this->election_topic,
            'type' => $this->type,
            'position' => $this->position,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'nominate_period' => $this->nominate_period,
            'status' => $this->status
        ]);

        session()->flash('success', 'Election Updated Successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function view($id)
    {
        $election = Election::findOrFail($id);
        $this->election_id = $election->election_id;
        $this->election_topic = $election->election_topic;
        $this->type = $election->type;
        $this->position = $election->position;
        $this->description = $election->description;
        $this->start_date = $election->start_date;
        $this->end_date = $election->end_date;
        $this->nominate_period = $election->nominate_period;
        $this->status = $election->status;

        $this->isView = true;
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
        $this->position = '';
        $this->description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->nominate_period = '';
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
}
