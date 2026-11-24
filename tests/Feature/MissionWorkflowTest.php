<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Mission;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\SchoolLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Policies\MissionPolicy;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_only_the_selected_teacher_can_respond_to_their_mission(): void
    {
        [$mission, $teacher, $otherTeacher, $schoolRecruiter, $outsider] = $this->scenario();
        $policy = new MissionPolicy();

        $this->assertTrue($policy->respond($teacher, $mission));
        $this->assertFalse($policy->respond($otherTeacher, $mission));
        $this->assertTrue($policy->view($schoolRecruiter, $mission));
        $this->assertFalse($policy->view($outsider, $mission));
        $this->assertTrue($policy->propose($schoolRecruiter, $mission));
        $this->assertFalse($policy->propose($outsider, $mission));
        $this->assertFalse($policy->propose($teacher, $mission));
    }

    private function scenario(): array
    {
        $teacher = $this->user('teacher');
        $otherTeacher = $this->user('teacher');
        $schoolRecruiter = $this->user('school_recruiter');
        $outsider = $this->user('school_recruiter');

        $school = School::create([
            'name' => 'École '.Str::random(8),
            'slug' => Str::slug('ecole-'.Str::random(8)),
            'city' => 'Dakar',
            'verification_status' => 'verified',
        ]);
        SchoolMember::create(['school_id' => $school->id, 'user_id' => $schoolRecruiter->id, 'role' => 'recruiter']);

        $profile = TeacherProfile::create([
            'user_id' => $teacher->id, 'first_name' => 'Mamadou', 'last_name' => 'Fall',
            'city' => 'Dakar', 'verification_status' => 'verified', 'is_available' => true,
        ]);
        $subject = Subject::create(['name' => 'Physique', 'slug' => 'physique']);
        $level = SchoolLevel::create(['name' => 'Lycée', 'slug' => 'lycee']);
        $offer = JobOffer::create([
            'school_id' => $school->id, 'created_by' => $schoolRecruiter->id,
            'title' => 'Professeur de physique', 'description' => 'Cours de physique au lycée.',
            'subject_id' => $subject->id, 'school_level_id' => $level->id,
            'city' => 'Dakar', 'status' => 'published',
        ]);
        $application = Application::create([
            'job_offer_id' => $offer->id, 'teacher_profile_id' => $profile->id,
            'status' => 'selected', 'submitted_at' => now(),
        ]);
        $mission = Mission::create([
            'application_id' => $application->id, 'proposed_by' => $schoolRecruiter->id,
            'starts_on' => now()->addWeek()->toDateString(), 'weekly_hours' => 12,
            'pay_amount_fcfa' => 80000, 'payment_frequency' => 'monthly',
            'mission_description' => 'Assurer les cours et préparer les évaluations.',
            'status' => 'planned', 'teacher_confirmation' => 'pending',
        ]);

        return [$mission, $teacher, $otherTeacher, $schoolRecruiter, $outsider];
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
