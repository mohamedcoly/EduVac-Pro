<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'account_type' => ['required', Rule::in(['teacher', 'school'])],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:10'],
            'first_name' => ['required_if:account_type,teacher', 'nullable', 'string', 'max:100'],
            'last_name' => ['required_if:account_type,teacher', 'nullable', 'string', 'max:100'],
            'school_name' => ['required_if:account_type,school', 'nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:100'],
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 10 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'email.unique' => 'Cette adresse e-mail possède déjà un compte.',
        ]);

        $user = DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => mb_strtolower($data['email']),
                'password' => $data['password'],
            ]);

            if ($data['account_type'] === 'teacher') {
                $user->assignRole('teacher');

                TeacherProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'] ?? null,
                    'city' => $data['city'] ?? 'Dakar',
                    'verification_status' => 'incomplete',
                ]);
            } else {
                $user->assignRole('school_manager');

                $schoolName = trim($data['school_name']);
                $school = School::create([
                    'name' => $schoolName,
                    'slug' => Str::slug($schoolName).'-'.Str::lower(Str::random(6)),
                    'email' => $user->email,
                    'phone' => $data['phone'] ?? null,
                    'city' => $data['city'] ?? 'Dakar',
                    'verification_status' => 'pending',
                ]);

                SchoolMember::create([
                    'school_id' => $school->id,
                    'user_id' => $user->id,
                    'role' => 'owner',
                ]);
            }

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice')
            ->with('status', 'Compte créé. Vérifiez votre adresse e-mail pour activer votre accès.');
    }
}
