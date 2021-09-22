<div class="flex space-x-4 w-full md:flex-col md:space-y-4 md:space-x-0 xl:flex-row xl:space-y-0 xl:space-x-4 h-18">
    <div class="flex flex-row py-3 px-6 bg-white rounded-xl dark:bg-theme-secondary-900">
        <div class="flex mr-2 w-full lg:w-1/2 xl:w-full">
            <div class="flex items-center pr-6 space-x-4 border-r border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="h-11 rounded-xl border-none loading-state circled-icon"></div>

                <div class="flex flex-col justify-center p-1 space-y-2">
                    <div class="flex items-center">
                        <div class="w-12 h-4 rounded-md loading-state"></div>
                    </div>

                    <span class="w-5 h-5 rounded-md loading-state"></span>
                </div>
            </div>

            <div class="flex items-center pr-6 ml-6 space-x-4 border-r border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="h-11 rounded-xl border-none loading-state circled-icon"></div>

                <div class="flex flex-col justify-center p-1 space-y-2">
                    <div class="flex items-center">
                        <div class="w-12 h-4 rounded-md loading-state"></div>
                    </div>

                    <span class="w-5 h-5 rounded-md loading-state"></span>
                </div>
            </div>

            <div class="flex items-center ml-6 space-x-4">
                <div class="h-11 rounded-xl border-none loading-state circled-icon"></div>

                <div class="flex flex-col justify-center p-1 space-y-2">
                    <div class="flex items-center">
                        <div class="w-20 h-4 rounded-md loading-state"></div>
                    </div>

                    <span class="w-5 h-5 rounded-md loading-state"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-row space-x-4 w-full">
        <div class="flex flex-grow py-3 px-6 bg-white rounded-xl dark:bg-theme-secondary-900">
            <div class="flex items-center mr-4 space-x-4">
                <div
                    class="h-11 rounded-xl border-none loading-state circled-icon">
                </div>

                <div class="flex flex-col justify-center p-1 space-y-2">
                    <div class="flex items-center">
                        <div class="h-4 rounded-md w-25 loading-state"></div>
                    </div>

                    <span class="h-5 rounded-md w-25 loading-state"></span>
                </div>
            </div>
        </div>

        <div class="flex flex-grow py-3 px-6 bg-white rounded-xl dark:bg-theme-secondary-900">
            <x-delegates.skeletons.data-boxes-next-delegate />
        </div>
    </div>
</div>
