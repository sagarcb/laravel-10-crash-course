<?php
use Illuminate\Support\Str
?>
<x-app-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <h1 class=" text-lg font-bold">{{ $ticket->title }}</h1>
        <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class=" flex justify-between py-4">
                <p>{{ $ticket->description }}</p>
                <p>{{ $ticket->created_at->diffForHumans() }}</p>
                @if ($ticket->attachment)
                    <a href="{{ '/storage/' . $ticket->attachment }}" target="_blank">Attachment</a>
                @endif
            </div>

            <div class="flex justify-between">
                <div class="flex">
                    <a href="{{ route('ticket.edit', $ticket->id) }}">
                        <x-primary-button>Edit</x-primary-button>
                    </a>

                    <form class="ml-2" action="{{ route('ticket.destroy', $ticket->id) }}" method="post">
                        @method('delete')
                        @csrf
                        <x-primary-button>Delete</x-primary-button>
                    </form>
                </div>
                @if (auth()->user()->isAdmin)
                    <div class="flex">
                        <form action="{{ route('ticket.update.status', $ticket->id) }}" method="post">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="status" value="resolved" />
                            <x-primary-button>Resolve</x-primary-button>
                        </form>
                        <form action="{{ route('ticket.update.status', $ticket->id) }}" method="post">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="status" value="rejected" />
                            <x-primary-button class="ml-2">Reject</x-primary-button>
                        </form>
                    </div>
                @else
                    <p class="">Status: {{ $ticket->status }} </p>
                @endif
            </div>
        </div>

        <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <ul>
                @foreach($ticket->replies as $reply)
                <li class="replies">
                    <div class="reply-text">
                        <span class="name">{{$reply->user->isAdmin? 'Admin' : Str::before($reply->user->name,' ')}}: </span> {{$reply->body}}
                    </div>
                    <div class="reply-time">
                        <p>{{$reply->created_at ? $reply->created_at->format('h:i:s A d/m/Y') : ''}}</p>
                    </div>
                </li>
                @endforeach
            </ul>

            <div class="reply-input-section">
                <form method="post" action="{{route('ticket.reply', $ticket->id)}}">
                    @csrf
                    <x-input-label for="reply" :value="__('Reply')" />
                    <x-text-input id="reply" class="block mt-1 w-full" type="text" name="body" value=""/>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-3">
                            Submit
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .replies {
        margin-bottom: 3px;
        border-bottom: 1px solid #c4c2c2;
        display: flex;
        justify-content: space-between;
        padding: 0 2px 2px 0;
    }
    .reply-input-section {
        margin-top: 5px;
    }
</style>
