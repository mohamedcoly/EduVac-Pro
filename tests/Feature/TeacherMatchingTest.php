<?php

namespace Tests\Feature;

use App\Models\JobOffer;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Subject;
use App\Models\TeacherAvailability;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\TeacherMatchingService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeacherMatchingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_matching_returns_one_hundred_for_a_complete_exact_match(): void
    {
        $teacherUser = $this->userWithRole('teacher');
        $schoolManager = $this->userWithRole('school_manager');
        $subject = Subject::create(['name' => 'Mathématiques', 'slug' => 'mathematiques']);
        $level = SchoolLevel::create(['name' => 'Lycée', 'slug' => 'lycee']);
        $school = School::create([
            'name' => 'Lycée Test',
            'slug' => 'lycee-test',
            'city' => 'Dakar',
            'verification_status' => 'verified',
        ]);

        $offer = JobOffer::create([
            'school_id' => $school->id,
            'created_by' => $schoolManager->id,
            'title' => 'Professeur de mathématiques',
            'description' => 'Assurer les cours de mathématiques au niveau lycée.',
            'subject_id' => $subject->id,
            'school_level_id' => $level->id,
            'city' => 'Dakar',
            'status' => 'published',
        ]);

        $profile = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'city' => 'Dakar',
            'years_experience' => 6,
            'is_available' => true,
            'verification_status' => 'verified',
            'mobility_areas' => ['Pikine'],
        ]);
        $profile->subjects()->attach($subject);
        $profile->schoolLevels()->attach($level);
        TeacherAvailability::create([
            'teacher_profile_id' => $profile->id,
            'weekday' => 1,
            'starts_at' => '08:00:00',
            'ends_at' => '12:00:00',
        ]);

        $matches = app(TeacherMatchingService::class)->rank($offer);

        $this->assertCount(1, $matches);
        $this->assertSame(100, $matches->first()['score']);
        $this->assertSame($profile->id, $matches->first()['profile']->id);
    }

    public function test_matching_rewards_a_declared_mobility_area(): void
    {
        $teacherUser = $this->userWithRole('teacher');
        $schoolManager = $this->userWithRole('school_manager');
        $subject = Subject::create(['name' => 'Français', 'slug' => 'francais']);
        $level = SchoolLevel::create(['name' => 'Collège', 'slug' => 'college']);
        $school = School::create([
            'name' => 'Collège Test',
            'slug' => 'college-test',
            'city' => 'Dakar',
            'verification_status' => 'verified',
        ]);
        $offer = JobOffer::create([
            'school_id' => $school->id,
            'created_by' => $schoolManager->id,
            'title' => 'Professeur de français',
            'description' => 'Assurer les cours de français au collège.',
            'subject_id' => $subject->id,
            'school_level_id' => $level->id,
            'city' => 'Pikine',
            'status' => 'published',
        ]);
        $profile = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Moussa',
            'last_name' => 'Fall',
            'city' => 'Dakar',
            'years_experience' => 2,
            'is_available' => true,
            'verification_status' => 'verified',
            'mobility_areas' => ['Pikine'],
        ]);
        $profile->subjects()->attach($subject);
        $profile->schoolLevels()->attach($level);

        $criteria = app(TeacherMatchingService::class)->score($profile->load(['subjects', 'schoolLevels', 'availabilities']), $offer);

        $this->assertSame(16, $criteria['location']['score']);
        $this->assertSame('Zone de mobilité déclarée', $criteria['location']['label']);
    }


    public function test_matching_excludes_unverified_or_unavailable_profiles(): void
    {
        $manager = $this->userWithRole('school_manager');
        $subject = Subject::create(['name' => 'Anglais', 'slug' => 'anglais']);
        $level = SchoolLevel::create(['name' => 'Primaire', 'slug' => 'primaire']);
        $school = School::create([
            'name' => 'École Test',
            'slug' => 'ecole-test',
            'city' => 'Dakar',
            'verification_status' => 'verified',
        ]);
        $offer = JobOffer::create([
            'school_id' => $school->id,
            'created_by' => $manager->id,
            'title' => 'Professeur d’anglais',
            'description' => 'Enseigner l’anglais aux élèves du primaire.',
            'subject_id' => $subject->id,
            'school_level_id' => $level->id,
            'city' => 'Dakar',
            'status' => 'published',
        ]);

        foreach ([
            ['verification_status' => 'pending_review', 'is_available' => true],
            ['verification_status' => 'verified', 'is_available' => false],
        ] as $index => $state) {
            TeacherProfile::create([
                'user_id' => $this->userWithRole('teacher')->id,
                'first_name' => 'Profil',
                'last_name' => 'Test '.$index,
                'city' => 'Dakar',
                'years_experience' => 1,
                'verification_status' => $state['verification_status'],
                'is_available' => $state['is_available'],
            ]);
        }

        $this->assertCount(0, app(TeacherMatchingService::class)->rank($offer));
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
}
