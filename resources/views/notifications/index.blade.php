<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>

            @if ($notifications->whereNull('read_at')->isNotEmpty())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                @forelse ($notifications as $notification)
                    <div class="border-b border-gray-100 p-5 {{ $notification->read_at ? 'bg-white' : 'bg-indigo-50/50' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ $notification->data['title'] ?? 'Notification EduVac Pro' }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <p class="mt-2 text-xs text-gray-400">
                                    {{ $notification->created_at?->diffForHumans() }}
                                    @unless ($notification->read_at)
                                        <span class="ml-2 font-medium text-indigo-600">Nouveau</span>
                                    @endunless
                                </p>
                            </div>

                            @unless ($notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Consulter
                                    </button>
                                </form>
                            @else
                                <a href="{{ $notification->data['url'] ?? route('dashboard') }}"
                                   class="shrink-0 text-sm font-medium text-gray-500 hover:text-indigo-600">
                                    Ouvrir
                                </a>
                            @endunless
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center">
                        <p class="font-medium text-gray-700">Vous êtes à jour.</p>
                        <p class="mt-1 text-sm text-gray-500">Vos notifications apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
