<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolMember;
use App\Models\User;
use App\Policies\JobOfferPolicy;
use App\Policies\SchoolPolicy;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_school_member_can_view_their_school_but_an_unrelated_user_cannot(): void
    {
        $owner = $this->userWithRole('school_manager');
        $outsider = $this->userWithRole('school_manager');
        $school = $this->school('verified');

        SchoolMember::create([
            'school_id' => $school->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $policy = new SchoolPolicy();

        $this->assertTrue($policy->view($owner, $school));
        $this->assertFalse($policy->view($outsider, $school));
    }

    public function test_only_owner_or_manager_can_update_a_school(): void
    {
        $owner = $this->userWithRole('school_manager');
        $recruiter = $this->userWithRole('school_recruiter');
        $school = $this->school('verified');

        SchoolMember::create([
            'school_id' => $school->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);
        SchoolMember::create([
            'school_id' => $school->id,
            'user_id' => $recruiter->id,
            'role' => 'recruiter',
        ]);

        $policy = new SchoolPolicy();

        $this->assertTrue($policy->update($owner, $school));
        $this->assertFalse($policy->update($recruiter, $school));
    }

    public function test_offer_creation_requires_membership_in_a_verified_school(): void
    {
        $manager = $this->userWithRole('school_manager');
        $pendingSchool = $this->school('pending');
        $verifiedSchool = $this->school('verified');

        SchoolMember::create([
            'school_id' => $pendingSchool->id,
            'user_id' => $manager->id,
            'role' => 'owner',
        ]);

        $policy = new JobOfferPolicy();
        $this->assertFalse($policy->create($manager));

        SchoolMember::where('school_id', $pendingSchool->id)
            ->where('user_id', $manager->id)
            ->delete();

        SchoolMember::create([
            'school_id' => $verifiedSchool->id,
            'user_id' => $manager->id,
            'role' => 'owner',
        ]);

        $this->assertTrue($policy->create($manager));
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

    private function school(string $status): School
    {
        return School::create([
            'name' => 'School '.Str::random(8),
            'slug' => Str::slug('school-'.Str::random(8)),
            'city' => 'Dakar',
            'verification_status' => $status,
        ]);
    }
}
