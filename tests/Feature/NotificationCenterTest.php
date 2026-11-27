<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EduVacActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_their_notifications(): void
    {
        $user = User::factory()->create();
        $user->notify(new EduVacActivityNotification(
            'Nouvelle candidature',
            'Une candidature a été reçue.',
            route('school-applications.index'),
            'application_submitted',
        ));

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Nouvelle candidature')
            ->assertSee('Une candidature a été reçue.');
    }

    public function test_user_can_mark_one_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new EduVacActivityNotification(
            'Entretien planifié',
            'Un entretien est prévu.',
            route('dashboard'),
            'interview_scheduled',
        ));

        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $owner->notify(new EduVacActivityNotification(
            'Message privé',
            'Cette notification appartient à un autre compte.',
            route('dashboard'),
            'private_test',
        ));

        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($otherUser)
            ->patch(route('notifications.read', $notification->id))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }
}
