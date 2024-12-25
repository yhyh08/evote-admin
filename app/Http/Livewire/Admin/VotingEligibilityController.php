<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\VotingEligibilityImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\VotingEligibility;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
                $this->errorMessage = 'The following phones are already uploaded: ' . 
                    collect($duplicatePhones)->map(function($phone) {
                        return "{$phone}";
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

    public function checkVoter($phone)
    {
        try {
            Log::info('Checking voter eligibility for: ' . $phone);
            
            $voter = VotingEligibility::where('phone', $phone)->first();
            
            return response()->json([
                'exists' => !is_null($voter)
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking voter: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error checking voter eligibility',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendOTP(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required'
            ]);

            // Check if voter exists
            $voter = VotingEligibility::where('phone', $request->phone)->first();
            
            if (!$voter) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number not found in voter list'
                ], 404);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP in cache for 10 minutes
            Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(10));

            // Send via Twilio
            $accountSid = 'ACd71d933d211fe37d288c1878567def95';
            $authToken = '49d14fe845f93a4d3395f37fbac18de2';
            $twilioNumber = '+15103300932';

            $client = new Client($account_sid, $auth_token);
            
            $message = $client->messages->create(
                $request->phone,
                [
                    'from' => $twilio_number,
                    'body' => "Your OTP for voting is: $otp. Valid for 10 minutes."
                ]
            );

            Log::info('OTP sent successfully to: ' . $request->phone);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending OTP: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP'
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required',
                'otp' => 'required'
            ]);

            // Get cached OTP
            $cachedOtp = Cache::get('otp_' . $request->phone);
            
            if (!$cachedOtp) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired'
                ], 400);
            }

            if ($request->otp !== $cachedOtp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            // Get voter details
            $voter = VotingEligibility::where('phone', $request->phone)->first();
            
            // Generate token
            $token = $voter->createToken('auth_token')->plainTextToken;

            // Clear used OTP
            Cache::forget('otp_' . $request->phone);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $voter
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying OTP: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Verification failed'
            ], 500);
        }
    }
}
