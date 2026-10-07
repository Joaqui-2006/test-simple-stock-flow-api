<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Simple Stock Flow - Spec-Driven Development');
})->purpose('Display inspirational quote');