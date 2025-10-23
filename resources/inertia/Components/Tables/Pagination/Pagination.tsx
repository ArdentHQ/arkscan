import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import { router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import PaginationMiddle from "./PaginationMiddle";
import PaginationArrow from "./PaginationArrow";
import DoubleChevronLeftIcon from "@ui/icons/arrows/double-chevron-left.svg?react";
import ChevronLeftSmallIcon from "@ui/icons/arrows/chevron-left-small.svg?react";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import DoubleChevronRightIcon from "@ui/icons/arrows/double-chevron-right.svg?react";
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

        const defaultPage = 1;
        const defaultPerPage = 25;
        const sanitizedParams: Record<string, string> = {
            ...urlParams,
        };

        if (pageNumber !== defaultPage) {
            sanitizedParams[pageName] = pageNumber.toString();
        }

        if (perPage === defaultPerPage) {
            delete sanitizedParams['per-page'];
        } else if (perPage !== undefined) {
            sanitizedParams['per-page'] = perPage.toString();
        }

        let url = paginator.path;
        if (Object.keys(sanitizedParams).length > 0) {
            url += '?' + new URLSearchParams(sanitizedParams).toString();
        }

        router.push({
            url,
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
                    onSubmit={(page: number) => gotoPage(page)}
                />

                <div className="flex space-x-2 w-full sm:w-auto">
                    <PaginationArrow
                        icon={DoubleChevronLeftIcon}
                        text={t('pagination.first')}
                        disabled={disabled || paginator.current_page === 1}
                        onClick={() => gotoPage(1)}
                    />

                    <PaginationArrow
                        icon={ChevronLeftSmallIcon}
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
                        onSubmit={(page: number) => gotoPage(page)}
                    />

                    <PaginationArrow
                        icon={ChevronRightSmallIcon}
                        disabled={disabled || paginator.current_page === paginator.last_page}
                        onClick={() => gotoPage(paginator.current_page + 1)}
                    />

                    <PaginationArrow
                        icon={DoubleChevronRightIcon}
                        text={t('pagination.last')}
                        disabled={disabled || paginator.current_page === paginator.last_page}
                        onClick={() => gotoPage(paginator.last_page)}
                    />
                </div>
            </div>
        </div>
    )
};
