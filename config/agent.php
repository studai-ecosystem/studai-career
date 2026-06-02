<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Autonomous Agent Operational Limits
    |--------------------------------------------------------------------------
    |
    | Safety rails for the autonomous job-application agent. These caps prevent
    | the agent from flooding the queue worker when many active configurations
    | exist or when the worker falls behind.
    |
    */

    // Queue connection/queue name the agent jobs run on. Used to measure depth.
    'queue' => env('AGENT_QUEUE', 'default'),

    // Maximum number of pending jobs allowed on the agent queue before the
    // hourly discovery cycle stops dispatching new SubmitApplicationsJob work.
    // The skipped configs are simply retried on the next scheduled cycle.
    'max_queue_depth' => (int) env('AGENT_MAX_QUEUE_DEPTH', 250),

];
