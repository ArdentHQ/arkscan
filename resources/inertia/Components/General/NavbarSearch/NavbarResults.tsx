import TruncateMiddle from "@/Components/General/TruncateMiddle";
import useShareData from "@/hooks/use-shared-data";
import type {
    INavbarSearchBlockResultData,
    INavbarSearchTransactionResultData,
    INavbarSearchWalletResultData,
} from "@/types/generated";
import { currencyWithDecimals } from "@/utils/number-formatter";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import Tooltip from "@/Components/General/Tooltip";
import { useNavbar } from "../Navbar/NavbarContext";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import CrossIcon from "@ui/icons/cross.svg?react";
import { useEffect, useRef, useState } from "react";

type SearchResultData =
    | INavbarSearchWalletResultData
    | INavbarSearchBlockResultData
    | INavbarSearchTransactionResultData;

export type SearchResult<TData = SearchResultData> = {
    type: string;
    url: string | null;
    identifier: string | null;
    data: TData;
};

interface NavbarResultsProps {
    onBlur: (event: React.FocusEvent<HTMLElement>) => void;
}

export default function NavbarResults({ onBlur }: NavbarResultsProps) {
    const { t } = useTranslation();

    const { query, results, hasResults, isLoading, searchModalOpen } = useNavbar();

    const open = query.length > 0 || searchModalOpen;

    const hasVisibleResults = hasResults && results.length > 0;

    return (
        <div
            className={classNames(
                "search-dropdown absolute right-0 top-9 z-10 mt-2 origin-top-right rounded-xl border border-transparent bg-white py-1 shadow-lg transition-all duration-150 dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:text-theme-dark-200",
                {
                    "pointer-events-auto scale-100 opacity-100": open,
                    "pointer-events-none scale-95 opacity-0": !open,
                    "w-[560px]": !hasVisibleResults,
                    "w-[628px]": hasVisibleResults,
                },
            )}
        >
            {open && (
                <div className="custom-scroll flex max-h-[410px] flex-col space-y-1 divide-y divide-dashed divide-theme-secondary-300 overflow-y-auto whitespace-nowrap px-6 py-3 text-sm font-semibold dark:divide-theme-dark-800">
                    {isLoading && (
                        <p className="text-center text-theme-secondary-900 dark:text-theme-dark-50">
                            {t("general.search.results_will_show_up")}
                        </p>
                    )}

                    {!isLoading && results.length === 0 && (
                        <p className="text-center text-theme-secondary-900 dark:text-theme-dark-50">
                            {t("general.search.no_results")}
                        </p>
                    )}

                    {!isLoading &&
                        results.map((result, index) => (
                            <div
                                key={`${result.type}-${result.identifier ?? result.url}`}
                                className={classNames("select-none", {
                                    "pt-1": index > 0,
                                })}
                            >
                                <ResultLink result={result} onBlur={onBlur}>
                                    {renderResult(result)}
                                </ResultLink>
                            </div>
                        ))}
                </div>
            )}
        </div>
    );
}

const SearchInput = () => {
    const { t } = useTranslation();
    const { query, setQuery } = useNavbar();
    const searchInputRef = useRef<HTMLInputElement>(null);
    const [localValue, setLocalValue] = useState(query);
    const debounceTimeoutRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        setLocalValue(query);
    }, [query]);

    useEffect(() => {
        return () => {
            if (debounceTimeoutRef.current) {
                clearTimeout(debounceTimeoutRef.current);
            }
        };
    }, []);

    const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const { value } = event.target;
        setLocalValue(value);

        if (debounceTimeoutRef.current) {
            clearTimeout(debounceTimeoutRef.current);
        }

        debounceTimeoutRef.current = setTimeout(() => {
            setQuery(value);
        }, 500);
    };

    const handleClear = () => {
        setLocalValue("");
        setQuery("");
        searchInputRef.current?.focus();
    };

    useEffect(() => {
        if (searchInputRef.current) {
            searchInputRef.current.focus();
        }
    }, []);

    return (
        <div className="group relative flex h-8 flex-shrink-0 items-center overflow-hidden rounded border-2 border-theme-secondary-300 focus-within:border-theme-primary-600 hover:border-theme-primary-600 dark:border-theme-dark-800 focus-within:dark:border-theme-primary-600 group-hover:dark:border-theme-primary-600">
            <div className="flex items-center pl-4 pr-2">
                <MagnifyingGlassSmallIcon className="h-4 w-4 text-theme-secondary-500 dim:text-theme-dark-200 dark:text-theme-dark-600" />
            </div>

            <div className="h-full flex-1 leading-none">
                <input
                    ref={searchInputRef}
                    type="text"
                    className="block h-full w-full overflow-ellipsis py-2 text-theme-secondary-900 dim:text-theme-dark-50 dark:bg-theme-dark-900 dark:text-theme-dark-200"
                    // wire:keydown.enter="goToFirstResult"
                    placeholder={t("general.navbar.search_placeholder")}
                    value={localValue}
                    onChange={handleChange}
                />
            </div>

            {query && (
                <button
                    type="button"
                    onClick={handleClear}
                    className="button-secondary -my-px bg-transparent pr-4 text-theme-secondary-700 dim:bg-transparent dim:text-theme-dark-50 dim:shadow-none dark:bg-theme-dark-900 dark:text-theme-dark-600"
                >
                    <CrossIcon className="h-3 w-3" />
                </button>
            )}
        </div>
    );
};

export function NavbarResultsMobile() {
    const { t } = useTranslation();

    const { query, results, hasResults, searchModalOpen, clear } = useNavbar();

    const handleBlur = () => {
        clear();
    };

    if (!searchModalOpen) {
        return <></>;
    }

    return (
        <div
            // x-ref="modal"
            // x-data="Modal.livewire({
            //     query: @entangle('query').live,
            //     searching: false,
            //     initSearch() {
            //         this.$nextTick(() => {
            //             this.focusSearchInput();
            //         });
            //     },
            //     getScrollable() {
            //         const { searchResults } = this.$refs;
            //         return searchResults;
            //     },
            //     focusSearchInput(){
            //         const { input } = this.$refs;
            //         input.focus();
            //     },
            // }, { disableFocusTrap: true })"
            className="custom-scroll container fixed inset-0 z-50 mx-auto flex h-screen w-full flex-col overflow-auto outline-none md:hidden"
            tabIndex={0}
            onKeyDown={(e) => {
                if (e.key === "Escape") {
                    clear();
                }
            }}
        >
            <div
                onClick={clear}
                className="fixed inset-0 bg-theme-secondary-900 opacity-70 dark:bg-theme-dark-800 dark:opacity-80"
            ></div>

            <div className="relative mx-4 my-6 flex flex-col rounded-xl border border-transparent bg-white p-6 dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:text-theme-dark-200 sm:m-8">
                <SearchInput />

                <div className="flex flex-col space-y-1 divide-y divide-dashed divide-theme-secondary-300 whitespace-nowrap text-sm font-semibold dark:divide-theme-dark-800">
                    {hasResults && (
                        <>
                            {results.map((result) => (
                                <div key={result.identifier} className="pt-1">
                                    <ResultLink result={result} onBlur={handleBlur}>
                                        {renderResult(result)}
                                    </ResultLink>
                                    {/* @if (is_a($result->model(), \App\Models\Wallet::class))
                                    <x-search.results.wallet :wallet="$result" truncate :truncate-length="14" />
                                @elseif (is_a($result->model(), \App\Models\Block::class))
                                    <x-search.results.block :block="$result" />
                                @elseif (is_a($result->model(), \App\Models\Transaction::class))
                                    <x-search.results.transaction :transaction="$result" />
                                @endif */}
                                </div>
                            ))}
                        </>
                    )}
                    {!hasResults && (
                        <div className="mt-4 whitespace-normal text-center text-theme-secondary-900 dark:text-theme-dark-50">
                            <p>
                                {query.length > 0
                                    ? t("general.search.no_results")
                                    : t("general.search.results_will_show_up")}
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

function ResultLink({
    result,
    children,
    onBlur,
}: {
    result: SearchResult;
    children: React.ReactNode;
    onBlur: (event: React.FocusEvent<HTMLElement>) => void;
}) {
    const href = result.url ?? "#";

    return (
        <a
            href={href}
            className="group/result transition-default -mx-3 block min-w-0 cursor-pointer rounded-[10px] p-3 hover:bg-theme-secondary-200 dark:hover:bg-black"
            onBlur={onBlur}
        >
            {children}
        </a>
    );
}

function renderResult(result: SearchResult) {
    if (result.type === "wallet") {
        return <WalletResult result={result as SearchResult<INavbarSearchWalletResultData>} />;
    }

    if (result.type === "block") {
        return <BlockResult result={result as SearchResult<INavbarSearchBlockResultData>} />;
    }

    if (result.type === "transaction") {
        return <TransactionResult result={result as SearchResult<INavbarSearchTransactionResultData>} />;
    }
}

function MobileResult({ header, children }: { header: React.ReactNode; children: React.ReactNode }) {
    return (
        <div className="rounded border border-theme-secondary-300 text-sm dark:border-theme-dark-700 md:hidden">
            <div className="flex items-center justify-between rounded-t bg-theme-secondary-100 px-4 py-3 dark:bg-theme-dark-950">
                {header}
            </div>
            <div className="flex flex-col space-y-4 px-4 pb-4 pt-3 sm:flex-1 sm:flex-row sm:justify-between sm:space-y-0">
                {children}
            </div>
        </div>
    );
}

function MobileResultDetail({ title, children }: { title: React.ReactNode; children: React.ReactNode }) {
    return (
        <div className="flex flex-col space-y-2 font-semibold">
            <div className="whitespace-nowrap text-xs leading-3.75 text-theme-secondary-700 dark:text-theme-dark-200">
                {title}
            </div>

            <div className="text-xs leading-3.75 text-theme-secondary-900 dark:text-theme-dark-50">{children}</div>
        </div>
    );
}

function WalletResult({ result }: { result: SearchResult<INavbarSearchWalletResultData> }) {
    const { t } = useTranslation();
    const { network } = useShareData();

    const hasUsername = result.data.username !== null;

    return (
        <>
            <MobileResult
                header={
                    <>
                        <div className="link font-semibold hover:text-theme-primary-600 group-hover/result:no-underline">
                            {hasUsername ? result.data.username : result.data.address}
                        </div>

                        {hasUsername && (
                            <div className="ml-1 truncate text-theme-secondary-700 dark:text-theme-dark-200">
                                {result.data.address}
                            </div>
                        )}
                    </>
                }
                children={
                    <MobileResultDetail title={t("general.search.balance_currency", { currency: network!.currency })}>
                        {currencyWithDecimals({
                            value: result.data.balance ?? 0,
                            currency: network!.currency,
                            hideCurrency: true,
                        })}
                    </MobileResultDetail>
                }
            />

            <div className="hidden flex-col space-y-2 md:flex">
                <div className="isolate flex items-center space-x-2 overflow-auto">
                    <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {t("general.search.address")}
                    </div>

                    <div className="link font-semibold hover:text-theme-primary-600 group-hover/result:no-underline">
                        {hasUsername ? result.data.username : result.data.address}
                    </div>

                    {hasUsername && (
                        <div className="ml-1 truncate text-theme-secondary-700 dark:text-theme-dark-200">
                            {result.data.address}
                        </div>
                    )}
                </div>

                <div className="flex items-center space-x-1 text-xs">
                    <div className="text-theme-secondary-700 dark:text-theme-dark-200">
                        {t("general.search.balance")}
                    </div>

                    <div className="truncate text-theme-secondary-900 dark:text-theme-dark-50">
                        {currencyWithDecimals({ value: result.data.balance ?? 0, currency: network!.currency })}
                    </div>
                </div>
            </div>
        </>
    );
}

function BlockResult({ result }: { result: SearchResult<INavbarSearchBlockResultData> }) {
    const { t } = useTranslation();

    const { validator, hash, transactionCount } = result.data;

    return (
        <>
            <>{/* TODO: add  mobile view (https://app.clickup.com/t/86dygw9uw) */}</>

            <div className="hidden flex-col space-y-2 md:flex">
                <div className="flex items-center space-x-2">
                    <div className="text-theme-secondary-900 dark:text-theme-dark-50">{t("general.search.block")}</div>

                    <div className="link min-w-0 hover:text-theme-primary-600 group-hover/result:no-underline">
                        <TruncateMiddle length={20}>{hash}</TruncateMiddle>
                    </div>
                </div>

                <div className="flex flex-col space-y-2 md:flex-row md:items-center md:space-x-4 md:space-y-0">
                    <div className="isolate flex items-center space-x-2 text-xs">
                        <div className="text-theme-secondary-700 dark:text-theme-dark-200">
                            {t("general.search.generated_by")}
                        </div>

                        <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                            <TruncateMiddle length={10}>{validator?.address}</TruncateMiddle>
                        </div>
                    </div>

                    <div className="flex items-center space-x-1 text-xs">
                        <div className="text-theme-secondary-700 dark:text-theme-dark-200">
                            {t("general.search.transactions")}
                        </div>

                        <div className="text-theme-secondary-900 dark:text-theme-dark-50">{transactionCount ?? 0}</div>
                    </div>
                </div>
            </div>
        </>
    );
}

const TransactionResultBadge = ({ className, children }: { className?: string; children: React.ReactNode }) => {
    return (
        <div
            className={classNames(
                "encapsulated-badge shrink-0 rounded border border-transparent bg-theme-secondary-200 px-[3px] py-[2px] text-center text-xs font-semibold leading-3.75 text-theme-secondary-700 dark:border-theme-dark-700 dark:bg-transparent dark:text-theme-dark-200",
                className,
            )}
        >
            {children}
        </div>
    );
};

function TransactionResult({ result }: { result: SearchResult<INavbarSearchTransactionResultData> }) {
    const { t } = useTranslation();
    const { network } = useShareData();

    const votedValidatorLabel = result.data.votedValidatorLabel;

    return (
        <>
            <>{/* TODO: add  mobile view (https://app.clickup.com/t/86dygw9uw) */}</>

            <div className="hidden flex-col space-y-2 md:flex">
                <div className="flex items-center space-x-2">
                    <TransactionResultBadge className="min-w-[92px]">
                        <Tooltip
                            disabled={!votedValidatorLabel}
                            content={
                                <div
                                    dangerouslySetInnerHTML={
                                        votedValidatorLabel
                                            ? {
                                                  __html: t("general.transaction.vote_validator", {
                                                      validator: votedValidatorLabel,
                                                  }),
                                              }
                                            : undefined
                                    }
                                />
                            }
                        >
                            <span>{result.data.typeName}</span>
                        </Tooltip>
                    </TransactionResultBadge>

                    <div className="link min-w-0 flex-1 hover:text-theme-primary-600 group-hover/result:no-underline">
                        <TruncateMiddle length={20}>{result.data.hash}</TruncateMiddle>
                    </div>
                </div>

                <div className="flex flex-col space-y-2 md:flex-row md:items-center md:space-x-4 md:space-y-0">
                    <div className="isolate flex items-center space-x-2 text-xs">
                        <TransactionResultBadge>{t("general.search.from")}</TransactionResultBadge>

                        {result.data.isVote || result.data.isUnvote ? (
                            <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                                <TruncateMiddle length={10}>{result.data.votedValidatorLabel}</TruncateMiddle>
                            </div>
                        ) : (
                            <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                                <TruncateMiddle length={10}>{result.data.sender?.address}</TruncateMiddle>
                            </div>
                        )}
                    </div>

                    <div className="isolate flex items-center space-x-2 text-xs">
                        <TransactionResultBadge>{t("general.search.to")}</TransactionResultBadge>

                        {result.data.isTransfer || result.data.isTokenTransfer ? (
                            <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                                <TruncateMiddle length={10}>{result.data.recipient?.address}</TruncateMiddle>
                            </div>
                        ) : (
                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                {t("general.search.contract")}
                            </span>
                        )}
                    </div>

                    <div className="flex items-center space-x-2 text-xs md:flex-1 md:justify-end md:space-x-0">
                        <div className="text-theme-secondary-500 dark:text-theme-dark-200 md:hidden">
                            {t("general.search.amount")}
                        </div>

                        <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                            {currencyWithDecimals({
                                value: result.data.amountWithFee ?? 0,
                                currency: network!.currency,
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
