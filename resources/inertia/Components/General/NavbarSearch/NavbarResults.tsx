import * as Popover from "@radix-ui/react-popover";
import { useTranslation } from "react-i18next";

export default function NavbarResults({
    onOpenChange,
    query,
}: {
    onOpenChange: (open: boolean) => void;
    query: string;
}) {
    const { t } = useTranslation();

    return (
        <Popover.Root open={!!query} onOpenChange={onOpenChange}>
            <Popover.Portal>
                <Popover.Content
                    side="bottom"
                    align="end"
                    sideOffset={8}
                    className={`/* Animaciones como x-transition */ data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:zoom-out-95 z-10 w-[560px] rounded-xl border border-transparent bg-white shadow-lg duration-100 dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:text-theme-dark-200`}
                >
                    <div className="custom-scroll flex max-h-[410px] flex-col space-y-1 divide-y divide-dashed divide-theme-secondary-300 overflow-y-auto whitespace-nowrap px-6 py-3 text-sm font-semibold dark:divide-theme-dark-800">
                        <p className="text-center text-theme-secondary-900 dark:text-theme-dark-50">
                            We could not find anything matching your search criteria, please try again!
                        </p>
                    </div>
                </Popover.Content>
            </Popover.Portal>
        </Popover.Root>
    );
}
