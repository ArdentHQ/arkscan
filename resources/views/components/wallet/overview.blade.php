<div class="md:px-10 md:pb-6 md:mx-auto md:max-w-7xl">
    <div class="flex flex-col md:space-y-3 md-lg:space-y-0 md-lg:flex-row md-lg:space-x-3">
        <x-wallet.overview.wallet :wallet="$wallet" />

        <x-wallet.overview.validator :wallet="$wallet" />
    </div>
</div>
