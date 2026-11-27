<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_messaging_inbox_requires_authentication(): void
    {
        $this->get(route('messages.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_empty_messaging_inbox(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('messages.index'))->assertOk()->assertSee('Messagerie');
    }

    public function test_user_cannot_open_conversation_without_participant_membership(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/messages/987654321')->assertNotFound();
    }
}
