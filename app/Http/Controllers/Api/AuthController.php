<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class AuthController extends Controller
{
    private $maxRetries = 3;
    private $retryDelay = 5; // seconds
    
    public function checkUser($phone)
    {
        try {
            Log::info('Checking user for phone: ' . $phone);
            
            $user = User::where('phone', $phone)->first();
            Log::info('User found: ' . ($user ? 'yes' : 'no'));
            
            return response()->json([
                'exists' => !is_null($user),
                'message' => !is_null($user) ? 'User found' : 'User not found'
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking user: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error checking user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function sendTwilioMessage($client, $phone, $message, $attempt = 1)
    {
        try {
            $twilioMessage = $client->messages->create(
                $phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                    'statusCallback' => config('services.twilio.status_callback', null) // Add status callback URL in your config
                ]
            );

            // Check message status
            $startTime = time();
            while (time() - $startTime < 30) { // Wait up to 30 seconds for delivery
                $messageStatus = $client->messages($twilioMessage->sid)->fetch()->status;
                
                if ($messageStatus === 'delivered') {
                    Log::info("Message delivered successfully on attempt {$attempt}");
                    return ['success' => true, 'message' => 'Message delivered'];
                }
                
                if (in_array($messageStatus, ['failed', 'undelivered'])) {
                    throw new TwilioException("Message {$messageStatus}");
                }
                
                sleep(2);
            }
            
            throw new TwilioException("Message delivery timeout");
            
        } catch (TwilioException $e) {
            Log::warning("Twilio attempt {$attempt} failed: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendOTP(Request $request)
    {
        try {
            Log::info('Send OTP request received: ' . json_encode($request->all()));
            
            $request->validate([
                'phone' => 'required'
            ]);

            $user = User::where('phone', $request->phone)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number not registered'
                ], 404);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Log::info('Generated OTP: ' . $otp . ' for phone: ' . $request->phone);
            
            // Store OTP in cache with attempts counter
            Cache::put('otp_' . $request->phone, [
                'code' => $otp,
                'attempts' => 0
            ], now()->addMinutes(10));

            // Verify Twilio credentials exist
            if (!config('services.twilio.sid') || !config('services.twilio.token') || !config('services.twilio.from')) {
                Log::error('Twilio credentials are missing');
                return response()->json([
                    'status' => false,
                    'message' => 'SMS service configuration error'
                ], 500);
            }

            $client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $message = "Your login OTP is: $otp. Valid for 10 minutes.";
            
            // Implement retry logic
            for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
                $result = $this->sendTwilioMessage($client, $request->phone, $message, $attempt);
                
                if ($result['success']) {
                    return response()->json([
                        'status' => true,
                        'message' => 'OTP sent successfully'
                    ]);
                }
                
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay);
                }
            }

            // If we're in development, return the OTP for testing
            if (app()->environment('local')) {
                return response()->json([
                    'status' => true,
                    'message' => 'OTP generated (Twilio disabled in development)',
                    'debug_otp' => $otp
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP after multiple attempts'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error in sendOTP: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            Log::info('Verify OTP request received: ' . json_encode($request->all()));
            
            $request->validate([
                'phone' => 'required',
                'otp' => 'required'
            ]);

            // Get cached OTP data
            $cachedData = Cache::get('otp_' . $request->phone);
            
            if (!$cachedData) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired'
                ], 400);
            }

            // Increment attempt counter
            $attempts = $cachedData['attempts'] + 1;
            if ($attempts >= 3) {
                Cache::forget('otp_' . $request->phone);
                return response()->json([
                    'status' => false,
                    'message' => 'Too many invalid attempts. Please request a new OTP.'
                ], 400);
            }

            // Update attempts in cache
            Cache::put('otp_' . $request->phone, [
                'code' => $cachedData['code'],
                'attempts' => $attempts
            ], now()->addMinutes(10));

            if ($request->otp !== $cachedData['code']) {
                Log::info('OTP mismatch. Received: ' . $request->otp . ', Expected: ' . $cachedData['code']);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            // Get user details
            $user = User::where('phone', $request->phone)->first();
            Log::info('User found for token generation: ' . ($user ? 'yes' : 'no'));
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Clear used OTP
            Cache::forget('otp_' . $request->phone);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'fullname' => $user->fullname,
                    'phone' => $user->phone,
                    'role_id' => $user->role_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in verifyOTP: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserInfo($phone)
    {
        $user = User::where('phone', $phone)->first();
        if ($user) {
            return response()->json([
                'name' => $user->name,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
        } else {
            return response()->json(['error' => 'User not found']);
        }
    }
}