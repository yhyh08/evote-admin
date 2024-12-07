<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ElectionCommittee;

class ElectionCommitteeController extends Component
{
    public $com_name, $com_phone, $com_email, $isApprove;
    public $committee_id;
    public $isOpen = false;
    public $isView = false;
    public $confirmingDeletion = false;

    protected $rules = [
        'com_name' => 'required|min:3',
        'com_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
        'com_email' => 'required|email',
        'isApprove' => 'required'
    ];

    protected $messages = [
        'com_name.required' => 'The election committee name is required.',
        'com_name.min' => 'The name must be at least 3 characters.',
        'com_phone.required' => 'The phone number is required.',
        'com_phone.regex' => 'Please enter a valid phone number.',
        'com_phone.min' => 'The phone number must be at least 10 digits.',
        'com_email.required' => 'The email address is required.',
        'com_email.email' => 'Please enter a valid email address.',
        'isApprove.required' => 'Please select a status.'
    ];

    public function render()
    {
        return view('livewire.admin.election-committee', [
            'committees' => ElectionCommittee::latest()->get()
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

        ElectionCommittee::create([
            'com_name' => $this->com_name,
            'com_phone' => $this->com_phone,
            'com_email' => $this->com_email,
            'isApprove' => $this->isApprove
        ]);

        session()->flash('success', 'Election Committee Member Created Successfully.');
        $this->dispatch('alert-shown');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $committee = ElectionCommittee::findOrFail($id);
        $this->committee_id = $committee->com_id;
        $this->com_name = $committee->com_name;
        $this->com_phone = $committee->com_phone;
        $this->com_email = $committee->com_email;
        $this->isApprove = $committee->isApprove;

        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        ElectionCommittee::where('com_id', $this->committee_id)->update([
            'com_name' => $this->com_name,
            'com_phone' => $this->com_phone,
            'com_email' => $this->com_email,
            'isApprove' => $this->isApprove
        ]);

        session()->flash('success', 'Election Committee Member Updated Successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function view($id)
    {
        $committee = ElectionCommittee::findOrFail($id);
        $this->committee_id = $committee->com_id;
        $this->com_name = $committee->com_name;
        $this->com_phone = $committee->com_phone;
        $this->com_email = $committee->com_email;
        $this->isApprove = $committee->isApprove;

        $this->isView = true;
        $this->openModal();
    }

    public function confirmDelete($com_id)
    {
        $this->committee_id = $com_id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        ElectionCommittee::find($this->committee_id)->delete();
        session()->flash('success', 'Election Committee Member Deleted Successfully.');
        $this->confirmingDeletion = false;
    }

    private function resetInputFields()
    {
        $this->com_name = '';
        $this->com_phone = '';
        $this->com_email = '';
        $this->isApprove = '';
        $this->committee_id = null;
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
