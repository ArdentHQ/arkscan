import { IPaginatedResponse } from "@/types";
import classNames from "classnames";
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
import { useEffect, useState } from "react";

export default function Pagination({
    paginator,
    className = "",
    tableRef,
}: {
    className?: string;
    paginator: IPaginatedResponse<any>;
    tableRef?: React.RefObject<HTMLDivElement | null>;
}) {
    const { t } = useTranslation();
    const { refreshPage, setIsLoading, isLoading: disabled } = usePageHandler();
    const [scrollTop, setScrollTop] = useState(0);

    const { pageName, urlParams } = paginator.meta;

    useEffect(() => {
        if (!tableRef?.current) {
            return;
        }

        const updateScrollTop = () => {
            const navbarHeight = document.querySelector("#navbar")?.clientHeight ?? 0;

            setScrollTop(Math.max((tableRef?.current?.offsetTop ?? 0) - navbarHeight));
        };

        updateScrollTop();

        window.addEventListener("resize", updateScrollTop);

        return () => {
            window.removeEventListener("resize", updateScrollTop);
        };
    }, [tableRef]);

    const gotoPage = (pageNumber: number, perPage?: number) => {
        setIsLoading(true);

        const defaultPage = 1;
        const defaultPerPage = 25;
        const sanitizedParams: Record<string, string> = {
            ...urlParams,
        };

        if (pageNumber !== defaultPage) {
            sanitizedParams[pageName] = pageNumber.toString();
        }

        if (perPage === defaultPerPage) {
            delete sanitizedParams["per-page"];
        } else if (perPage !== undefined) {
            sanitizedParams["per-page"] = perPage.toString();
        }

        let url = paginator.path;
        if (Object.keys(sanitizedParams).length > 0) {
            url += "?" + new URLSearchParams(sanitizedParams).toString();
        }

        router.push({
            url,
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                refreshPage(() => {
                    setIsLoading(false);

                    setTimeout(() => {
                        window.scrollTo({
                            top: scrollTop,
                            behavior: "smooth",
                        });
                    }, 0);
                });
            },
        });
    };

    return (
        <div className="-mx-6 my-4 flex flex-col items-center space-y-6 rounded-b-xl border-t border-theme-secondary-300 px-6 pt-4 dark:border-theme-dark-700 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 md:mx-0 md:mb-8 md:mt-0 md:border md:border-t-0 md:pb-4">
            <div className="flex items-center space-x-2 text-sm font-semibold dark:text-theme-dark-200 sm:mr-8">
                <span>{t("pagination.show")}</span>

                <PerPageDropdown
                    disabled={disabled}
                    onChange={(perPage: number) => gotoPage(1, perPage)}
                    paginator={paginator}
                />

                <span>{t("pagination.records")}</span>
            </div>

            <div
                className={classNames({
                    "pagination-wrapper relative flex w-full flex-col justify-between sm:w-auto sm:flex-row": true,
                    [className]: true,
                })}
            >
                <PaginationMiddle
                    className="relative mb-2 flex w-full sm:hidden"
                    paginator={paginator}
                    disabled={disabled}
                    onSubmit={(page: number) => gotoPage(page)}
                />

                <div className="flex w-full space-x-2 sm:w-auto">
                    <PaginationArrow
                        testId="pagination:first-page"
                        icon={DoubleChevronLeftIcon}
                        text={t("pagination.first")}
                        disabled={disabled || paginator.current_page === 1}
                        onClick={() => gotoPage(1)}
                    />

                    <PaginationArrow
                        testId="pagination:previous-page"
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
                        testId="pagination:next-page"
                        icon={ChevronRightSmallIcon}
                        disabled={disabled || paginator.current_page === paginator.last_page}
                        onClick={() => gotoPage(paginator.current_page + 1)}
                    />

                    <PaginationArrow
                        testId="pagination:last-page"
                        icon={DoubleChevronRightIcon}
                        text={t("pagination.last")}
                        disabled={disabled || paginator.current_page === paginator.last_page}
                        onClick={() => gotoPage(paginator.last_page)}
                    />
                </div>
            </div>
        </div>
    );
}
