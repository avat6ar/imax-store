<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginGoogleRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\VerificationRequest;
use App\Jobs\VerificationJob;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  /**
   * Create a new user.
   *
   * @param  SignupRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */

  public function signup(SignupRequest $request)
  {
    $data = $request->validated();
    $locale = $request->header('Accept-Language') ?? 'en';

    $checkEmail = User::where('email', $data['email'])->first();

    if ($checkEmail)
    {
      if ($checkEmail->socail_status)
      {
        return response()->json(['errors' => ["email" => "You already used this email to sign up with a social account. Please switch to social login or enter a new email address"]], 422);
      }
      return response()->json(['errors' => ["email" => "The email has already been taken."]], 422);
    }

    $user = User::create([
      "name" => $data["name"],
      "email" => $data["email"],
      "password" => Hash::make($data["password"]),
    ]);

    $code = $this->generateCode($user);

    VerificationJob::dispatch($user, $code);

    return response()->json([
      "message" => "User created successfully. Verify your email to activate your account.",
      "email" => $user->email,
    ]);
  }

  /**
   * Authenticate a user.
   *
   * @param  LoginRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(LoginRequest $request)
  {
    $credentials = $request->validated();
    $locale = $request->header('Accept-Language') ?? 'en';

    if (!Auth::attempt($credentials))
    {
      return response()->json(['errors' => "The email or password you entered doesn't match our records. Please double-check and try again"], 404);
    }

    $user = User::where('id', auth()->user()->id)->first();

    if (!$user->email_verified_at)
    {
      $code = $this->generateCode($user);
      VerificationJob::dispatch($user, $code);
      return response()->json(['message' => 'Your email address is not verified. Please verify your email.', 'email' => $user->email, 'verify_email' => false], 422);
    }

    if ($user->status == false)
    {
      return response()->json(['errors' => "Your account is not active. Please contact the administrator."], 404);
    }

    $token = $user->createToken("login")->accessToken;

    return response()->json([
      "message" => "User login successfully",
      "user" => $user,
      "token" => $token,
    ]);
  }

  public function loginGoogle(LoginGoogleRequest $request)
  {
    $credentials = $request->validated();

    $user = User::where('email', $credentials['email'])
      ->orWhere('social_id', $credentials['sub'])
      ->first();

    if ($user)
    {
      $user->update([
        'name' => $credentials['name'],
        'email' => $credentials['email'],
        'email_verified_at' => $credentials['email_verified'] ? now() : null,
        'social_id' => $credentials['sub'],
        'social_type' => 'google',
      ]);
    }
    else
    {
      $user = User::create([
        'name' => $credentials['name'],
        'email' => $credentials['email'],
        'email_verified_at' => $credentials['email_verified'] ? now() : null,
        'social_id' => $credentials['sub'],
        'social_type' => 'google',
        'password' => Hash::make('google'),
        'social_status' => true
      ]);
    }

    Auth::login($user);

    $token = $user->createToken('google')->accessToken;

    return response()->json([
      "message" => "User logged in successfully",
      "user" => $user,
      "token" => $token,
    ]);
  }


  /**
   * Logout user and revoke token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    $user = auth()->user();
    $user->tokens->each(function ($token, $key)
    {
      $token->delete();
    });

    return response()->json(['message' => 'Logged out successfully']);
  }
  /**
   * Get authenticated user's profile.
   *
   * @param  Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function profile(Request $request)
  {
    $user = $request->user();

    if ($user->status == false)
    {
      return response()->json(['errors' => "Your account is not active. Please contact the administrator."], 404);
    }

    return response()->json(['message' => 'User profile successfully', 'user' => $user]);
  }

  /**
   * Generate verification code for user.
   *
   * @param  User  $user
   * @return VerificationCode
   */
  public function generateCode(User $user)
  {
    $user->verificationCode()->delete();

    return $user->verificationCode()->create([
      'code' => Str::random(8),
      'expire_date' => now()->addMinutes(30),
    ]);
  }

  public function resendCode(Request $request)
  {
    $locale = $request->header('Accept-Language') ?? 'en';
    $validate = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
    ]);

    if ($validate->fails())
    {
      return response()->json(['errors' => $validate->errors()], 404);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user)
    {
      return response()->json(['email' => 'Invalid email address'], 404);
    }

    if ($user->email_verified_at)
    {
      $token = $user->createToken("login")->accessToken;

      return response()->json([
        "message" => "User login successfully",
        "user" => $user,
        "token" => $token,
      ]);
    }

    $code = $this->generateCode($user);
    VerificationJob::dispatch($user, $code);

    return response()->json(['message' => 'Verification code sent successfully']);
  }

  public function resendCodePassword(Request $request)
  {
    $locale = $request->header('Accept-Language') ?? 'en';
    $validate = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
    ]);

    if ($validate->fails())
    {
      return response()->json(['errors' => $validate->errors()], 404);
    }

    $user = User::where('email', $request->email)->first();

    $code = $this->generateCode($user);
    VerificationJob::dispatch($user, $code);

    return response()->json(['message' => 'Verification code sent successfully']);
  }

  public function changePassword(ChangePasswordRequest $request)
  {
    $data = $request->validated();

    $user = User::where('id', $request->user()->id)->first();

    if (Hash::check($data['old_password'], $user->password))
    {
      $user->password = Hash::make($data['new_password']);
      $user->save();

      return response()->json(['message' => 'Password changed successfully'], 200);
    }
    else
    {
      return response()->json(['message' => 'Old password is incorrect'], 422);
    }
  }

  public function forogetPassword(Request $request)
  {
    $locale = $request->header('Accept-Language') ?? 'en';
    $validate = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
    ]);

    if ($validate->fails())
    {
      return response()->json(['errors' => $validate->errors()], 404);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user)
    {
      return response()->json(['message' => 'User not found'], 404);
    }

    $code = $this->generateCode($user);

    VerificationJob::dispatch($user, $code);

    return response()->json(['message' => 'Password reset email sent successfully', 'email' => $user->email]);
  }

  /**
   * Verify user's email.
   *
   * @param  Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function verification(VerificationRequest $request)
  {
    $data = $request->validated();

    $user = User::where('email', $data['email'])->first();

    if (!$user)
    {
      return response()->json(['errors' => 'User not found'], 404);
    }

    if ($user->email_verified_at)
    {
      $token = $user->createToken('login')->accessToken;
      return response()->json(['message' => 'Email verified successfully', 'user' => $user, 'token' => $token]);
    }

    $verificationCode = VerificationCode::where('user_id', $user->id)
      ->where('code', $data['code'])
      ->where('expire_date', '>', now())
      ->first();

    if (!$verificationCode)
    {
      return response()->json(['errors' => 'Invalid or expired verification code'], 422);
    }

    $user->email_verified_at = now();
    $user->save();

    $verificationCode->delete();

    Auth::login($user);

    $token = $user->createToken('login')->accessToken;

    return response()->json(['message' => 'Email verified successfully', 'user' => $user, 'token' => $token]);
  }

  public function resetPassword(ResetPasswordRequest $request)
  {
    $data = $request->validated();

    $user = User::where('email', $data['email'])->first();

    if (!$user)
    {
      return response()->json(['errors' => 'User not found'], 404);
    }

    $verificationCode = VerificationCode::where('user_id', $user->id)
      ->where('code', $data['code'])
      ->where('expire_date', '>', now())
      ->first();

    if (!$verificationCode)
    {
      return response()->json(['errors' => 'Invalid or expired verification code'], 422);
    }

    $user->password = Hash::make($data['password']);
    $user->email_verified_at = now();
    $user->save();

    $verificationCode->delete();
    $token = $user->createToken('login')->accessToken;

    return response()->json(['message' => 'Password has been reset successfully', 'user' => $user, 'token' => $token]);
  }
}
