<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('User Avatar') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Add or update user avatar.") }}
        </p>
    </header>
    <img width="50" height="50" class="rounded-full" src="{{"/storage/" . $user->avatar}}" alt="User Avatar">

    <form action="{{route('profile.avatar.ai')}}" method="post" class="mt-4">
        @csrf
        @method('patch')
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Generate Avatar from AI.") }}
        </p>

        <x-primary-button>{{ __('Generate Avatar') }}</x-primary-button>
    </form>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        {{ __("Or") }}
    </p>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form method="post" action="{{ route('profile.avatar') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Upload Avatar from Computer')" />
            <x-text-input id="avatar" name="avatar" type="file" class="mt-1 block w-full" :value="old('avatar', $user->avatar)" required autofocus autocomplete="avatar" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
