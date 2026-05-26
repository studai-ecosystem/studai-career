<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Events\UserRegistered as UserRegisteredEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'          => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type'      => ['required', 'string', 'in:job_seeker,employer'],
            'phone'             => ['nullable', 'string', 'max:20'],
            // Employer-only fields
            'company_name'      => ['required_if:account_type,employer', 'nullable', 'string', 'max:255'],
            'industry'          => ['nullable', 'string', 'max:100'],
            'company_size'      => ['nullable', 'string', 'max:50'],
            'company_website'   => ['nullable', 'string', 'max:255'],
            'hr_email'          => ['required_if:account_type,employer', 'nullable', 'email', 'max:255'],
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'account_type' => $validated['account_type'],
            'phone'        => $validated['phone'] ?? null,
        ]);

        // If employer: create Company record and link it to the user
        if ($validated['account_type'] === 'employer' && !empty($validated['company_name'])) {
            $slug = $this->generateUniqueSlug($validated['company_name']);

            $company = Company::create([
                'name'         => $validated['company_name'],
                'slug'         => $slug,
                'industry'     => $validated['industry'] ?? null,
                'company_size' => $validated['company_size'] ?? null,
                'website'      => $validated['company_website'] ?? null,
                'hr_email'     => $validated['hr_email'] ?? null,
                'is_verified'  => false,
            ]);

            $user->update(['company_id' => $company->id]);
        }

        event(new Registered($user));
        event(new UserRegisteredEvent($user));  // fires welcome email listener
        Auth::login($user);

        // Redirect employers to onboarding, job seekers to dashboard
        if ($user->account_type === 'employer') {
            return redirect()->route('employer.onboarding');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Generate a unique slug for the company name.
     */
    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Company::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
