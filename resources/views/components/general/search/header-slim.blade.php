<div class="flex flex-col space-y-5 w-full">
    <div
        x-data="{ searchFocused: false }"
        @search-slim-expand="searchFocused = true"
        @search-slim-close="searchFocused = false"
        class="flex relative justify-between items-center"
    >
        <h1
            class="header-2"
            :class="{ hidden: searchFocused }"
        >
            {{ $title }}
        </h1>
    </div>
</div>
