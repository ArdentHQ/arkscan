import { IExchange } from "@/types/generated";
import ArrowExternalIcon from "@ui/icons/arrows/arrow-external.svg?react";

export default function HeaderExternalLink({ exchange }: { exchange: IExchange }) {
    return (
        <a
            href={exchange.url}
            className="-mx-4 -my-3 flex flex-1 items-center justify-between px-4 py-3"
            target="_blank"
            rel="noopener nofollow noreferrer"
        >
            <div className="flex items-center space-x-2">
                <div className="flex h-8 w-8 items-center justify-center rounded-full border border-theme-secondary-200 bg-white p-1.5 dark:border-theme-dark-900 dark:bg-theme-dark-900">
                    <img className="max-h-full max-w-full" src={exchange.iconUrl} alt="" />
                </div>

                <span className="text-sm font-semibold leading-4 text-theme-primary-600 dark:text-theme-dark-50">
                    {exchange.name}
                </span>
            </div>

            <ArrowExternalIcon className="h-4 w-4 text-theme-secondary-500 dark:text-theme-dark-500" />
        </a>
    );
}
