<div class="md:px-8 md:pb-6 md:mx-auto md:max-w-7xl lg:px-10">
    <div class="flex flex-col md:flex-row md:space-x-3">
        <x-wallet.overview.wallet :wallet="$wallet" />

        <x-wallet.overview.delegate :wallet="$wallet" />
    </div>
</div>
