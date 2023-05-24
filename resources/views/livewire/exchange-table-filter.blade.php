<div class="flex flex-col w-full space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3 md-lg:w-auto">
    <x-exchanges.filter-dropdown :selected="$selectedType" :options="$types" icon="app-stack" param="type" />

    <x-exchanges.filter-dropdown :selected="$selectedPair" :options="$pairs" icon="app-pair" param="pair" />
</div>
