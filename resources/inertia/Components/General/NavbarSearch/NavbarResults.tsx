import TruncateMiddle from "@/Components/General/TruncateMiddle";
import useConfig from "@/hooks/use-config";
import type {
    INavbarSearchBlockResultData,
    INavbarSearchMemoryWallet,
    INavbarSearchTransactionResultData,
    INavbarSearchWalletResultData,
} from "@/types/generated";
import { currencyWithDecimals } from "@/utils/number-formatter";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import Tooltip from "@/Components/General/Tooltip";

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
    query: string;
    results: SearchResult[];
    hasResults: boolean;
    isLoading: boolean;
    onBlur: () => void;
}

export default function NavbarResults({ query, results, hasResults, isLoading, onBlur }: NavbarResultsProps) {
    const { t } = useTranslation();

    const open = query.length > 0;
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

function ResultLink({
    result,
    children,
    onBlur,
}: {
    result: SearchResult;
    children: React.ReactNode;
    onBlur: () => void;
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

function WalletResult({ result }: { result: SearchResult<INavbarSearchWalletResultData> }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    const hasUsername = result.data.username !== null;

    return (
        <>
            <>{/* TODO: add  mobile view */}</>

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
                        {currencyWithDecimals(result.data.balance ?? 0, network!.currency)}
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
            <>{/* TODO: add  mobile view */}</>

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
    const { network } = useConfig();

    const votedValidatorLabel = result.data.votedValidatorLabel;

    return (
        <>
            <>{/* TODO: add  mobile view */}</>

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
                            {currencyWithDecimals(result.data.amountWithFee ?? 0, network!.currency)}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
