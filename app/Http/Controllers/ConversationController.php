<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\EduVacActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = Conversation::accessibleBy($request->user())
            ->with(['school', 'teacherProfile.user', 'participants'])
            ->withCount(['messages as unread_count' => fn ($q) => $q
                ->where('sender_id', '!=', $request->user()->id)
                ->whereNull('read_at')])
            ->orderByDesc('last_message_at')
            ->paginate(15);

        return view('messages.index', compact('conversations'));
    }

    public function start(Request $request, Application $application): RedirectResponse
    {
        $user = $request->user();
        $application = Application::visibleTo($user)->with(['jobOffer.school.members.user', 'teacherProfile.user'])->whereKey($application->id)->firstOrFail();
        $teacher = $application->teacherProfile?->user;
        $school = $application->jobOffer?->school;

        if (! $teacher || ! $school) {
            abort(404);
        }

        $isTeacher = $teacher->is($user);
        $isSchoolMember = $school->members->contains(fn ($member) => $member->user?->is($user));
        if (! $isTeacher && ! $isSchoolMember) {
            abort(403);
        }

        $conversation = DB::transaction(function () use ($application, $school, $teacher, $user) {
            $conversation = Conversation::firstOrCreate(
                ['application_id' => $application->id],
                ['school_id' => $school->id, 'teacher_profile_id' => $application->teacher_profile_id, 'created_by' => $user->id]
            );

            $conversation->participants()->syncWithoutDetaching([$teacher->id]);
            foreach ($school->members as $member) {
                if ($member->user) {
                    $conversation->participants()->syncWithoutDetaching([$member->user_id]);
                }
            }

            return $conversation;
        });

        return redirect()->route('messages.show', $conversation)->with('status', 'Conversation ouverte.');
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $conversation = Conversation::accessibleBy($request->user())
            ->with(['school', 'teacherProfile.user', 'messages.sender', 'participants'])
            ->whereKey($conversation->id)->firstOrFail();

        $conversation->participants()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')->update(['read_at' => now()]);

        return view('messages.show', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $conversation = Conversation::accessibleBy($request->user())->whereKey($conversation->id)->firstOrFail();
        $data = $request->validate(['body' => ['required', 'string', 'min:1', 'max:5000']]);
        $body = trim($data['body']);
        if ($body === '') {
            throw ValidationException::withMessages(['body' => 'Le message ne peut pas être vide.']);
        }

        $message = DB::transaction(function () use ($conversation, $request, $body) {
            $message = $conversation->messages()->create(['sender_id' => $request->user()->id, 'body' => $body]);
            $conversation->update(['last_message_at' => $message->created_at]);
            return $message;
        });

        $conversation->load(['school.members.user', 'teacherProfile.user']);
        $recipientIds = $conversation->participants()->where('users.id', '!=', $request->user()->id)->pluck('users.id')->unique();
        $recipientIds->each(function ($id) use ($request, $conversation) {
            $recipient = \App\Models\User::find($id);
            $recipient?->notify(new EduVacActivityNotification(
                'Nouveau message EduVac',
                $request->user()->name.' vous a envoyé un message.',
                route('messages.show', $conversation),
                'message_received'
            ));
        });

        return redirect()->route('messages.show', $conversation)->with('status', 'Message envoyé.');
    }
}
