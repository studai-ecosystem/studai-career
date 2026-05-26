<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\CompanyIntelligenceProfile;
use App\Services\AI\OrinOnboardingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Orin™ Conversational Employer Onboarding
 *
 * Replaces the static onboarding form with a multi-turn AI interview.
 * On completion, persists a Company Intelligence Profile.
 */
class OrinOnboardingController extends Controller
{
    public function __construct(private OrinOnboardingService $orin)
    {
        $this->middleware(['auth', 'employer']);
    }

    /**
     * Show the onboarding chat interface.
     */
    public function show(): View
    {
        $company = auth()->user()->company;
        $profile = $company
            ? CompanyIntelligenceProfile::where('company_id', $company->id)->first()
            : null;

        return view('employer.onboarding-chat', compact('company', 'profile'));
    }

    /**
     * API: process a chat turn and return Orin™'s response.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'history' => ['required', 'array'],
            'history.*.role' => ['required', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $company = auth()->user()->company;
        if (! $company) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

        $history = $request->input('history');
        $lastUserMsg = collect($history)->last()['content'] ?? '';

        // Check if user signalled completion
        if (strtoupper(trim($lastUserMsg)) === 'DONE' || str_contains(strtolower($lastUserMsg), 'done')) {
            $profile = $this->orin->extractProfile($history, $company);
            return response()->json([
                'message'  => "✅ Your Company Intelligence Profile has been saved! (Completeness: {$profile->completeness_score}%). You can now create job listings. Orin™ will use this profile to generate smarter JDs and candidate evaluations.",
                'complete' => true,
                'profile'  => [
                    'completeness_score' => $profile->completeness_score,
                    'industry'           => $profile->industry,
                ],
            ]);
        }

        $reply = $this->orin->nextMessage($history, $company);

        return response()->json([
            'message'  => $reply,
            'complete' => false,
        ]);
    }

    /**
     * Skip onboarding — mark as complete with minimal data.
     */
    public function skip(Request $request): JsonResponse
    {
        $company = auth()->user()->company;
        if ($company) {
            CompanyIntelligenceProfile::updateOrCreate(
                ['company_id' => $company->id],
                ['onboarding_complete' => true, 'completeness_score' => 10]
            );
        }
        return response()->json(['redirect' => route('employer.home')]);
    }
}
