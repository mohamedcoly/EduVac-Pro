<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Interview;
use App\Models\School;
use App\Models\TeacherDocument;
use App\Models\TeacherProfile;
use App\Policies\ApplicationPolicy;
use App\Policies\JobOfferPolicy;
use App\Policies\InterviewPolicy;
use App\Policies\SchoolPolicy;
use App\Policies\TeacherDocumentPolicy;
use App\Policies\TeacherProfilePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(School::class, SchoolPolicy::class);
        Gate::policy(TeacherProfile::class, TeacherProfilePolicy::class);
        Gate::policy(JobOffer::class, JobOfferPolicy::class);
        Gate::policy(Interview::class, InterviewPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(TeacherDocument::class, TeacherDocumentPolicy::class);
    }
}
