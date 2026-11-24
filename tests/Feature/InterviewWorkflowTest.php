<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Interview;
use App\Models\JobOffer;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Policies\InterviewPolicy;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InterviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_only_the_candidate_can_respond_to_their_interview(): void
    {
        [$application, $interview, $teacher, $otherTeacher] = $this->interviewScenario();

        $policy = new InterviewPolicy();

        $this->assertTrue($policy->respond($teacher, $interview));
        $this->assertFalse($policy->respond($otherTeacher, $interview));
    }

    public function test_only_a_member_of_the_recruiting_school_can_schedule_or_cancel(): void
    {
        [$application, $interview, $teacher, $otherTeacher, $recruiter, $outsider] = $this->interviewScenario();

        $policy = new InterviewPolicy();

        $this->assertTrue($policy->schedule($recruiter, $interview));
        $this->assertFalse($policy->schedule($outsider, $interview));
        $this->assertTrue($policy->view($teacher, $interview));
        $this->assertFalse($policy->view($outsider, $interview));
    }

    private function interviewScenario(): array
    {
        $teacher = $this->user('teacher');
        $otherTeacher = $this->user('teacher');
        $recruiter = $this->user('school_recruiter');
        $outsider = $this->user('school_recruiter');

        $school = School::create([
            'name' => 'Établissement '.Str::random(8),
            'slug' => Str::slug('ecole-'.Str::random(8)),
            'city' => 'Dakar',
            'verification_status' => 'verified',
        ]);
        SchoolMember::create(['school_id' => $school->id, 'user_id' => $recruiter->id, 'role' => 'recruiter']);

        $profile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'city' => 'Dakar',
            'verification_status' => 'verified',
            'is_available' => true,
        ]);

        $subject = \App\Models\Subject::create(['name' => 'Mathématiques', 'slug' => 'maths']);
        $level = \App\Models\SchoolLevel::create(['name' => 'Lycée', 'slug' => 'lycee']);
        $offer = JobOffer::create([
            'school_id' => $school->id,
            'created_by' => $recruiter->id,
            'title' => 'Professeur de mathématiques',
            'description' => 'Cours de mathématiques pour le cycle secondaire.',
            'subject_id' => $subject->id,
            'school_level_id' => $level->id,
            'city' => 'Dakar',
            'status' => 'published',
        ]);
        $application = Application::create([
            'job_offer_id' => $offer->id,
            'teacher_profile_id' => $profile->id,
            'status' => 'interview',
            'submitted_at' => now(),
        ]);
        $interview = Interview::create([
            'application_id' => $application->id,
            'scheduled_by' => $recruiter->id,
            'scheduled_at' => now()->addDays(2),
            'format' => 'online',
            'status' => 'scheduled',
            'teacher_response' => 'pending',
        ]);

        return [$application, $interview, $teacher, $otherTeacher, $recruiter, $outsider];
    }

    private function user(string $role): User
    {
        $user = User::create([
            'name' => 'Test '.Str::random(5),
            'email' => Str::uuid().'@example.test',
            'password' => 'A-strong-test-password-123',
        ]);
        $user->assignRole($role);
        return $user;
    }
}
