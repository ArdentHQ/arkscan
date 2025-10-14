import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import { useEffect, useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import CrossIcon from "@ui/icons/cross.svg?react";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import MagnifyingGlassIcon from "@ui/icons/magnifying-glass.svg?react";
import SquareReturnArrowIcon from "@ui/icons/square-return-arrow.svg?react";

export default function PaginationMiddle({
    paginator,
    disabled,
    className = '',
    onSubmit,
}: {
    paginator: IPaginatedResponse<any>;
    disabled?: boolean;
    className?: string | Record<string, boolean | CallableFunction>;
    onSubmit: (page: number) => void;
}) {
    const { t } = useTranslation();

    // const { urlParams } = paginator.meta;

    const [showSearch, setShowSearch] = useState(false);
    const searchRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        if (showSearch && searchRef.current) {
            searchRef.current.value = '';

            searchRef.current.focus();
        }
    }, [showSearch]);

    let classes = '';
    if (typeof className === 'object') {
        classes = classNames(className, { showSearch });
    } else {
        classes = className;
    }

    return (
        <div className={classes}>
            {showSearch && <div className="flex absolute left-0 z-10 space-x-2 w-full h-full bg-white dark:bg-theme-dark-900">
                <div className="flex overflow-hidden flex-1 items-center px-2 bg-white rounded outline outline-2 outline-theme-primary-600 dark:bg-theme-dark-900">
                    <MagnifyingGlassIcon className="w-5 h-5 text-theme-secondary-500 dark:text-theme-dark-700" />

                    <input
                        ref={searchRef}
                        type="number"
                        min="1"
                        max={paginator.last_page}
                        name={paginator.meta.pageName}
                        placeholder={t('actions.enter_the_page_number', { ns: 'ui' })}
                        className="py-2 px-3 w-full bg-transparent placeholder:dark:text-theme-dark-700 dark:text-theme-dark-200"
                        onChange={() => {
                            if (parseInt(searchRef.current!.value) > paginator.last_page) {
                                searchRef.current!.value = paginator.last_page.toString();
                            }
                        }}
                        onKeyDown={(event) => {
                            if (event.key !== 'Enter') {
                                return;
                            }

                            onSubmit(parseInt(searchRef.current!.value));

                            setShowSearch(false);
                        }}
                        onBlur={(event) => {
                            console.log('blur', event);
                            const blurredOutside = !event.target.contains(event.target);
                            if (blurredOutside) {
                                event.target.value = '';
                            }
                        }}
                    />

                    <SquareReturnArrowIcon className="hidden sm:block dark:text-theme-dark-600 w-4 h-4" />
                </div>

                <button
                    type="button"
                    className="p-2 button-secondary"
                    onClick={() => setShowSearch(false)}
                >
                    <CrossIcon className="w-4 h-4" />
                </button>
            </div>}

            <button
                onClick={() => setShowSearch(!showSearch)}
                type="button"
                className={classNames({
                    "inline-flex relative justify-center items-center p-0 w-full leading-5 button-secondary group/pagination focus:ring-theme-primary-500 focus:dark:ring-theme-dark-blue-300": true,
                    'opacity-0': showSearch,
                })}
                disabled={disabled || paginator.last_page === 1}
            >
                <div className="py-1.5 px-2 sm:px-3 md:px-4 group-hover/pagination:text-transparent">
                    {t('generic.pagination.current_to', {
                        currentPage: paginator.current_page.toLocaleString('us', {maximumFractionDigits: 0}),
                        lastPage: paginator.last_page.toLocaleString('us', {maximumFractionDigits: 0}),
                        ns: 'ui',
                    })}
                </div>

                <div className="absolute m-auto text-transparent group-hover/pagination:text-white">
                    <MagnifyingGlassSmallIcon className="w-4 h-4" />
                </div>
            </button>
        </div>
    )
}
