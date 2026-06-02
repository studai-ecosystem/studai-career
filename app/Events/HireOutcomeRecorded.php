<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\HireOutcome;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a terminal employer decision (hired / rejected) has been captured
 * as a HireOutcome. Decoupled so listeners can drive candidate learning paths
 * and feed offline S.C.O.U.T. threshold calibration without coupling to the
 * decision flow.
 */
class HireOutcomeRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(public HireOutcome $outcome)
    {
    }
}
