<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Data export concurrency
    |--------------------------------------------------------------------------
    |
    | Only one large export (e.g. "Database Seleksi Administrasi") runs at a time
    | to protect the shared-hosting PHP worker pool. A second request waits this
    | many seconds for the running export before being told to try again.
    |
    */

    'lock_wait_seconds' => (int) env('EXPORT_LOCK_WAIT_SECONDS', 20),

];
