<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('eduvac:about', function () {
    $this->info('EduVac Pro V2 — SaaS de recrutement scolaire.');
})->purpose('Display EduVac Pro application information');
