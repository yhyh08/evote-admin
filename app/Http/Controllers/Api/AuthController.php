<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class AuthController extends Controller
{
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
            
            // Store OTP in cache
            Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(10));

            // For development, return the OTP in response
            return response()->json([
                'status' => true,
                'message' => 'OTP generated successfully',
                'debug_otp' => $otp,  // Include OTP in response for development
                'phone' => $request->phone
            ]);

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

            // Get cached OTP
            $cachedOtp = Cache::get('otp_' . $request->phone);
            Log::info('Cached OTP exists: ' . ($cachedOtp ? 'yes' : 'no'));
            
            if (!$cachedOtp) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired'
                ], 400);
            }

            if ($request->otp !== $cachedOtp) {
                Log::info('OTP mismatch. Received: ' . $request->otp . ', Expected: ' . $cachedOtp);
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

            Log::info('Login successful for user: ' . $user->id);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error in verifyOTP: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}