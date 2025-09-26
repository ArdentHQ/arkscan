import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import { router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import PaginationMiddle from "./PaginationMiddle";
import PaginationArrow from "./PaginationArrow";
import DoubleChevronLeft from "@/Assets/Icons/Arrows/DoubleChevronLeft";
import ChevronLeftSmall from "@/Assets/Icons/Arrows/ChevronLeftSmall";
import ChevronRightSmall from "@/Assets/Icons/Arrows/ChevronRightSmall";
import DoubleChevronRight from "@/Assets/Icons/Arrows/DoubleChevronRight";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import PerPageDropdown from "./PerPageDropdown";
import { useState } from "react";

export default function Pagination({
    paginator,
    className = '',
    tableRef,
}: {
    className?: string;
    paginator: IPaginatedResponse<any>;
    tableRef: React.RefObject<HTMLDivElement | null>;
}) {
    const { t } = useTranslation();
    const { refreshPage } = usePageHandler();

    const { pageName, urlParams } = paginator.meta;

    const [disabled, setDisabled] = useState(false);

    const gotoPage = (pageNumber: number, perPage?: number) => {
        setDisabled(true);

        router.push({
            url: paginator.path + '?' + new URLSearchParams({
                ...urlParams,
                [pageName]: pageNumber,
                'per-page': (perPage ?? paginator.per_page).toString(),
            }),
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                refreshPage(() => {
                    setDisabled(false);

                    const navbarHeight = document.querySelector('#navbar')?.clientHeight ?? 0;

                    window.scrollTo({ top: Math.max((tableRef?.current?.offsetTop ?? 0) - navbarHeight), behavior: 'smooth' });
                });
            },
        });
    }

    return (
        <div className="-mx-6 px-6 border-t border-theme-secondary-300 dark:border-theme-dark-700 md:border-t-0 md:border rounded-b-xl mt-4 md:mt-0 pt-4 md:pb-4 md:mx-0 flex flex-col items-center space-y-6 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center space-x-2 text-sm font-semibold sm:mr-8 dark:text-theme-dark-200">
                <span>{t('pagination.show')}</span>

                <PerPageDropdown
                    disabled={disabled}
                    onChange={(perPage: number) => gotoPage(1, perPage)}
                    paginator={paginator}
                />

                <span>{t('pagination.records')}</span>
            </div>

            <div className={classNames({
                "relative pagination-wrapper flex justify-between flex-col sm:flex-row w-full sm:w-auto": true,
                [className]: true,
            })}>
                <PaginationMiddle
                    className="flex relative mb-2 w-full sm:hidden"
                    paginator={paginator}
                    disabled={disabled}
                />

                <div className="flex space-x-2 w-full sm:w-auto">
                    <PaginationArrow
                        icon={DoubleChevronLeft}
                        text={t('pagination.first')}
                        disabled={disabled || paginator.current_page === 1}
                        onClick={() => gotoPage(1)}
                    />

                    <PaginationArrow
                        icon={ChevronLeftSmall}
                        disabled={disabled || paginator.current_page === 1}
                        onClick={() => gotoPage(paginator.current_page - 1)}
                    />

                    <PaginationMiddle
                        className={{
                            "hidden sm:block": true,
                            "w-full max-w-[346px]": ({ showSearch }: { showSearch: boolean }) => showSearch,
                        }}
                        paginator={paginator}
                        disabled={disabled}
                    />

                    <PaginationArrow
                        icon={ChevronRightSmall}
                        disabled={disabled || paginator.current_page === paginator.last_page}
                        onClick={() => gotoPage(paginator.current_page + 1)}
                    />

                    <PaginationArrow
                        icon={DoubleChevronRight}
                        text={t('pagination.last')}
                        disabled={disabled || paginator.current_page === paginator.last_page}
                        onClick={() => gotoPage(paginator.last_page)}
                    />
                </div>
            </div>
        </div>
    )
};
