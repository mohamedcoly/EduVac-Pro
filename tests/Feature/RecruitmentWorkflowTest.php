<?php

namespace Tests\Feature;

use App\Models\JobOffer;
use App\Models\School;
use App\Models\SchoolMember;
use App\Models\SchoolLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Policies\ApplicationPolicy;
use App\Policies\JobOfferPolicy;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RecruitmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_teacher_can_view_verified_school_offer_before_profile_is_approved_but_cannot_apply(): void
    {
        $teacher = $this->userWithRole('teacher');
        $profile = TeacherProfile::create([
            'user_id' => $teacher->id,
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'city' => 'Dakar',
            'verification_status' => 'incomplete',
            'is_available' => true,
        ]);
        $school = $this->school();
        $subject = Subject::create(['name' => 'Mathématiques', 'slug' => 'mathematiques']);
        $level = SchoolLevel::create(['name' => 'Lycée', 'slug' => 'lycee']);
        $offer = JobOffer::create([
            'school_id' => $school->id,
            'created_by' => $this->userWithRole('school_manager')->id,
            'title' => 'Professeur de mathématiques',
            'description' => 'Assurer les cours de mathématiques au niveau lycée.',
            'subject_id' => $subject->id,
            'school_level_id' => $level->id,
            'city' => 'Dakar',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertTrue((new JobOfferPolicy())->view($teacher, $offer));
        $this->assertFalse((new ApplicationPolicy())->apply($teacher, $offer));
        $this->assertSame($teacher->id, $profile->user_id);
    }

    public function test_school_recruiter_can_only_access_their_own_school_offers(): void
    {
        $recruiter = $this->userWithRole('school_recruiter');
        $manager = $this->userWithRole('school_manager');
        $ownSchool = $this->school();
        $otherSchool = $this->school();

        SchoolMember::create(['school_id' => $ownSchool->id, 'user_id' => $recruiter->id, 'role' => 'recruiter']);
        $subject = Subject::create(['name' => 'Français', 'slug' => 'francais']);
        $level = SchoolLevel::create(['name' => 'Collège', 'slug' => 'college']);

        foreach ([$ownSchool, $otherSchool] as $school) {
            JobOffer::create([
                'school_id' => $school->id,
                'created_by' => $manager->id,
                'title' => 'Professeur de français',
                'description' => 'Cours de français pour les classes du collège.',
                'subject_id' => $subject->id,
                'school_level_id' => $level->id,
                'city' => 'Dakar',
                'status' => 'draft',
            ]);
        }

        $visibleIds = JobOffer::visibleTo($recruiter)->pluck('school_id')->all();
        $this->assertSame([$ownSchool->id], $visibleIds);
    }

    private function userWithRole(string $role): User
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => Str::uuid().'@example.test',
            'password' => 'A-strong-test-password-123',
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function school(): School
    {
        return School::create([
            'name' => 'School '.Str::random(8),
            'slug' => Str::slug('school-'.Str::random(8)),
            'city' => 'Dakar',
            'verification_status' => 'verified',
        ]);
    }
}
