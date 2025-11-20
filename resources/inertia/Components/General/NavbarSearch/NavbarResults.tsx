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

type SearchResultData =
    | INavbarSearchWalletResultData
    | INavbarSearchBlockResultData
    | INavbarSearchTransactionResultData
    | Record<string, any>;

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
}

export default function NavbarResults({ query, results, hasResults, isLoading }: NavbarResultsProps) {
    const { t } = useTranslation();

    const open = query.length > 0;
    const hasVisibleResults = hasResults && results.length > 0;

    return (
        <div
            className={classNames(
                "absolute right-0 top-9 z-10 mt-2 origin-top-right rounded-xl border border-transparent bg-white py-1 shadow-lg transition-all duration-150 dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:text-theme-dark-200",
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
                        results.map((result) => (
                            <ResultLink key={`${result.type}-${result.identifier ?? result.url}`} result={result}>
                                {renderResult(result)}
                            </ResultLink>
                        ))}
                </div>
            )}
        </div>
    );
}

function ResultLink({ result, children }: { result: SearchResult; children: React.ReactNode }) {
    const href = result.url ?? "#";

    return (
        <a
            href={href}
            className="group/result transition-default -mx-3 block min-w-0 cursor-pointer rounded-[10px] p-3 hover:bg-theme-secondary-200 dark:hover:bg-black"
            // @TODO: See what the blurHandler does and add it if necessary
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

    return <GenericResult result={result} />;
}

function WalletResult({ result }: { result: SearchResult<INavbarSearchWalletResultData> }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    const name = result.data.username ?? result.data.address ?? "";

    return (
        <>
            <div className="hidden flex-col space-y-2 md:flex">
                <div className="isolate flex items-center space-x-2 overflow-auto">
                    <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {t("general.search.address")}
                    </div>

                    <div className="link font-semibold hover:text-theme-primary-600 group-hover/result:no-underline">
                        {name}
                    </div>
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

    const validator = result.data.validator;
    const validatorLabel = validator?.username ?? validator?.address ?? t("general.search.generated_by");

    return (
        <div className="flex flex-col space-y-1 text-xs font-semibold">
            <div className="flex items-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
                <span>{t("general.search.block")}</span>
                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                    {result.data.hash ? <TruncateMiddle length={24}>{result.data.hash}</TruncateMiddle> : "-"}
                </span>
            </div>

            <div className="flex items-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
                <span>{t("general.search.generated_by")}</span>
                <span className="truncate text-theme-secondary-900 dark:text-theme-dark-50">{validatorLabel}</span>
            </div>

            <div className="flex items-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
                <span>{t("general.search.transactions")}</span>
                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                    {result.data.transactionCount ?? 0}
                </span>
            </div>
        </div>
    );
}

function TransactionResult({ result }: { result: SearchResult<INavbarSearchTransactionResultData> }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    const typeLabel = (() => {
        if (result.data.isVote) {
            return t("general.search.vote");
        }

        if (result.data.isUnvote) {
            return t("general.search.unvote");
        }

        return t("general.search.transaction");
    })();

    return (
        <div className="flex flex-col space-y-1 text-xs font-semibold">
            <div className="flex items-center space-x-3">
                <span className="rounded bg-theme-secondary-200 px-2 py-0.5 text-theme-secondary-900 dark:bg-theme-dark-800 dark:text-theme-dark-50">
                    {typeLabel}
                </span>
                <span className="min-w-0 truncate text-theme-secondary-900 dark:text-theme-dark-50">
                    {result.data.hash ? <TruncateMiddle length={22}>{result.data.hash}</TruncateMiddle> : "-"}
                </span>
            </div>

            <div className="flex items-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
                <span>{t("general.search.from")}</span>
                <span className="truncate text-theme-secondary-900 dark:text-theme-dark-50">
                    {formatWalletLabel(result.data.sender)}
                </span>
            </div>

            <div className="flex items-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
                <span>{t("general.search.to")}</span>
                <span className="truncate text-theme-secondary-900 dark:text-theme-dark-50">
                    {formatRecipientLabel(result.data.recipient, t)}
                </span>
            </div>

            <div className="flex items-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
                <span>{t("general.search.amount")}</span>
                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                    {currencyWithDecimals(result.data.amountWithFee ?? 0, network!.currency, 2, true, true)}
                </span>
            </div>
        </div>
    );
}

function GenericResult({ result }: { result: SearchResult }) {
    return (
        <div className="flex flex-col space-y-1 text-xs font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
            <span className="text-theme-secondary-900 dark:text-theme-dark-50">{result.type}</span>
            <span className="truncate">{result.identifier}</span>
        </div>
    );
}

function formatWalletLabel(wallet?: INavbarSearchMemoryWallet | null): string {
    if (!wallet) {
        return "-";
    }

    return wallet.username ?? wallet.address ?? "-";
}

function formatRecipientLabel(
    wallet: INavbarSearchMemoryWallet | null | undefined,
    t: ReturnType<typeof useTranslation>["t"],
): string {
    if (!wallet) {
        return "-";
    }

    if (wallet.isContract) {
        return t("general.search.contract");
    }

    return wallet.username ?? wallet.address ?? "-";
}
