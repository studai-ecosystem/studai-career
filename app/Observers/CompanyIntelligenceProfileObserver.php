<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CompanyIntelligenceProfile;
use App\Services\AI\Scout\CorporateDNADecoderService;

/**
 * F8: Keeps the cached Corporate DNA analysis in sync with the company
 * intelligence profile. Any change to the profile invalidates the 24h cache
 * so the next analysis reflects the updated organizational data.
 */
class CompanyIntelligenceProfileObserver
{
    public function saved(CompanyIntelligenceProfile $profile): void
    {
        $this->forget($profile);
    }

    public function deleted(CompanyIntelligenceProfile $profile): void
    {
        $this->forget($profile);
    }

    private function forget(CompanyIntelligenceProfile $profile): void
    {
        if ($profile->company_id !== null) {
            CorporateDNADecoderService::forgetCache((int) $profile->company_id);
        }
    }
}
