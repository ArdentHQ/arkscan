<div class="flex flex-col w-full space-y-5">
    <div
        x-data="{ searchFocused: false }"
        @search-slim-expand="searchFocused = true"
        @search-slim-close="searchFocused = false"
        class="relative flex items-center justify-between"
    >
        <h1
            class="font-bold whitespace-no-wrap header-2 text-theme-secondary-900 dark:text-theme-secondary-200"
            :class="{ hidden: searchFocused }"
        >
            {{ $title }}
        </h1>

        <div
            class="hidden md:block"
            :class="{
                'w-full': searchFocused,
                'w-1/2 lg:w-5/12 xl:w-7/12': ! searchFocused,
            }"
            x-cloak
        >
            <livewire:search-module :is-slim="true" />
        </div>
    </div>
</div>
