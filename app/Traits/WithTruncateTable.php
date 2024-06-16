<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait WithTruncateTable
{
    public function truncate($table): void
    {
        DB::table($table)->truncate();
    }
}
