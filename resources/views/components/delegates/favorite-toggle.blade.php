@props(['model'])

<div {{ $attributes }}>
    <button
        x-data="{
            isFavorite: {{ $model->isFavorite() ? 'true' : 'false' }},
        }"
        type="button"
        class="flex space-x-2 items-center font-semibold favorite-icon"
        :class="{
            'dark:text-theme-dark-300': ! isFavorite,
            'text-theme-primary-600 favorite-icon__selected': isFavorite,
        }"
        @click="function () {
            if (this.isFavorite) {
                Livewire.emit('removeFavoriteDelegate', '{{ $model->publicKey() }}');
            } else {
                Livewire.emit('setFavoriteDelegate', '{{ $model->publicKey() }}');
            }
            this.isFavorite = ! this.isFavorite;
        }"
    >
        <x-ark-icon name="app-favorite-star" />

        @if ($slot->isNotEmpty())
            <div class="leading-4.25">
                {{ $slot }}
            </div>
        @endif
    </button>
</div>
