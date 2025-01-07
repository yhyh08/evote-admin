<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Organization;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrganizationController extends Component
{
    use WithFileUploads;

    public $org_id, $org_name, $org_desc, $org_cat, $org_address, $org_img;
    public $pic_name, $pic_phone, $pic_email;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $deleteId;
    public $search = '';
    public $viewMode = false;
    public $selectedOrg;
    public $categories = [
        'Technology',
        'Other'
    ];
    public $confirmingActivation = false;
    public $activateId;
    public $org_website, $org_email, $org_size;

    protected $rules = [
        'org_name' => 'required|min:3',
        'org_desc' => 'required',
        'org_cat' => 'required',
        'org_address' => 'required',
        'org_img' => 'nullable|image|max:1024',
        'pic_name' => 'required|min:3',
        'pic_phone' => ['required', 'regex:/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/'],
        'pic_email' => 'required|email',
        'org_website' => 'required|url',
        'org_email' => 'required|email',
        'org_size' => 'required',
    ];

    protected function messages()
    {
        return [
            'pic_phone.required' => 'The phone number is required.',
            'pic_phone.regex' => 'The phone number must be a valid format',
        ];
    }

    public function render()
    {
        return view('livewire.admin.organization', [
            'organizations' => Organization::where('org_name', 'like', '%'.$this->search.'%')
                ->orderBy('created_at', 'desc')
                ->get()
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

        $data = [
            'org_name' => $this->org_name,
            'org_desc' => $this->org_desc,
            'org_cat' => $this->org_cat,
            'org_address' => $this->org_address,
            'pic_name' => $this->pic_name,
            'pic_phone' => $this->pic_phone,
            'pic_email' => $this->pic_email,
            'org_website' => $this->org_website,
            'org_email' => $this->org_email,
            'org_size' => $this->org_size,
        ];

        if ($this->org_img) {
            $data['org_img'] = $this->org_img->store('organizations', 'public');
        }

        if ($this->org_id) {
            $organization = Organization::find($this->org_id);
            if ($this->org_img && $organization->org_img) {
                Storage::disk('public')->delete($organization->org_img);
            }
            $organization->update($data);
        } else {
            Organization::create($data);
        }

        session()->flash('message', $this->org_id ? 'Organization updated.' : 'Organization created.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $this->viewMode = false;
        
        $this->org_id = $id;
        $organization = Organization::findOrFail($id);
        
        $this->org_name = $organization->org_name;
        $this->org_desc = $organization->org_desc;
        $this->org_cat = $organization->org_cat;
        $this->org_address = $organization->org_address;
        $this->pic_name = $organization->pic_name;
        $this->pic_phone = $organization->pic_phone;
        $this->pic_email = $organization->pic_email;
        $this->org_website = $organization->org_website;
        $this->org_email = $organization->org_email;
        $this->org_size = $organization->org_size;
        
        $this->isOpen = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->deleteId = $id;
        $this->viewMode = false;
        $this->selectedOrg = null;
    }

    public function delete()
    {
        $organization = Organization::find($this->deleteId);
        if ($organization->org_img) {
            Storage::disk('public')->delete($organization->org_img);
        }
        $organization->delete();
        
        session()->flash('message', 'Organization deleted');
        $this->confirmingDeletion = false;
    }

    private function resetInputFields()
    {
        $this->org_id = null;
        $this->org_name = '';
        $this->org_desc = '';
        $this->org_cat = '';
        $this->org_address = '';
        $this->org_img = null;
        $this->pic_name = '';
        $this->pic_phone = '';
        $this->pic_email = '';
        $this->org_website = '';
        $this->org_email = '';
        $this->org_size = '';
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function view($id)
    {
        $this->selectedOrg = Organization::findOrFail($id);
        $this->viewMode = true;
        
        if ($this->selectedOrg->org_img) {
            $this->selectedOrg->image_url = Storage::url($this->selectedOrg->org_img);
        }
    }

    public function closeView()
    {
        $this->viewMode = false;
        $this->selectedOrg = null;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function confirmActivate($id)
    {
        $this->confirmingActivation = true;
        $this->activateId = $id;
    }

    public function activate()
    {
        $organization = Organization::find($this->activateId);
        $organization->update(['is_active' => true]);
        
        session()->flash('message', 'Organization activated successfully.');
        $this->confirmingActivation = false;
    }

    public function closeActivateModal()
    {
        $this->confirmingActivation = false;
        $this->activateId = null;
    }

    public function getAllOrganizations(){
        $organizations = Organization::all();
        return response()->json($organizations);
    }

    public function getOrganizationInfo($id){
        $organization = Organization::find($id);
        return response()->json([
            'org_name' => $organization->org_name,
            'org_desc' => $organization->org_desc,
            'org_cat' => $organization->org_cat,
            'org_address' => $organization->org_address,
            'org_website' => $organization->org_website,
            'org_email' => $organization->org_email,
            'org_size' => $organization->org_size,
            'pic_name' => $organization->pic_name,
            'pic_phone' => $organization->pic_phone,
            'pic_email' => $organization->pic_email,
            'org_img' => $organization->org_img,
            'is_active' => $organization->is_active
        ]);
    }

    public function saveOrganization(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'org_name' => 'required|min:3',
                'org_desc' => 'required',
                'org_cat' => 'required',
                'org_address' => 'required',
                'org_img' => 'nullable|image|max:1024',
                'pic_name' => 'required|min:3',
                'pic_phone' => ['required', 'regex:/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/'],
                'pic_email' => 'required|email',
                'org_website' => 'required|string',
                'org_email' => 'required|email',
                'org_size' => 'required|string',
                'is_active' => 'nullable|boolean'
            ]);

            $data = $validatedData;
            
            if (!isset($data['is_active'])) {
                $data['is_active'] = false;
            }
            
            if ($request->hasFile('org_img')) {
                $data['org_img'] = $request->file('org_img')->store('organizations', 'public');
            }

            $organization = Organization::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Organization created successfully',
                'data' => $organization
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create organization',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
