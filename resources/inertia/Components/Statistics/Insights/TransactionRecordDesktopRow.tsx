import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import Number from "@/Components/General/Number";
import { StatisticsRecord } from "@/Pages/Statistics.contracts";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { networkCurrency } from "@/utils/number-formatter";
import { formatDate } from "@/utils/formatter";

export default function TransactionRecordDesktopRow({
    recordKey,
    record,
}: {
    recordKey: string;
    record: StatisticsRecord | null;
}) {
    const { t } = useTranslation();

    const headerTitle = t(`pages.statistics.insights.transactions.header.${recordKey}`);

    if (!record) {
        return (
            <div className="hidden w-full min-w-0 pt-1 md:flex xl:pt-0">
                <div className="flex w-full justify-between xl:w-[770px]">
                    <div className="flex flex-1">{headerTitle}</div>
                    <div className="md:w-50">{t("general.na")}</div>
                </div>
            </div>
        );
    }

    const isTransaction = record.type === "transaction";
    const isBlock = record.type === "block";

    const titleLabel = isTransaction
        ? t("pages.statistics.insights.transactions.header.transaction_id")
        : t("pages.statistics.insights.transactions.header.block");

    const amountLabel =
        recordKey === "most_transactions_in_block"
            ? t("pages.statistics.insights.transactions.header.transactions")
            : t("pages.statistics.insights.transactions.header.amount");

    const amountValue = (() => {
        if (recordKey === "most_transactions_in_block" && isBlock) {
            return <Number>{record.transactionCount ?? 0}</Number>;
        }

        if (recordKey === "highest_fee" && isBlock) {
            return record.fee !== undefined ? networkCurrency(record.fee, 2, true) : t("general.na");
        }

        if (isTransaction) {
            return networkCurrency(record.amount, 0, true);
        }

        return t("general.na");
    })();

    const dateFormatted = formatDate(record.timestamp);
    const recordUrl = record.type === "transaction" ? route("transaction", record.hash) : record.url;

    return (
        <div className="hidden w-full min-w-0 pt-1 md:flex xl:pt-0">
            <div className="flex w-full justify-between xl:w-[770px]">
                <div className="flex flex-1">{headerTitle}</div>
                <div className="md-lg:flex-2 md-lg:flex-row md-lg:space-y-0 flex flex-1 flex-col justify-between space-y-3">
                    <div className="flex flex-1 justify-between">
                        <span>{titleLabel}:</span>
                        <Link href={recordUrl} className="link">
                            {isTransaction ? (
                                <TruncateMiddle>{record.hash}</TruncateMiddle>
                            ) : (
                                <Number>{record.height}</Number>
                            )}
                        </Link>
                    </div>
                    <div className="md-lg:pl-16 flex w-full flex-1 flex-col space-y-3 xl:flex-row xl:space-y-0">
                        <div className="flex w-full flex-1 justify-between space-x-2">
                            <div>{amountLabel}:</div>
                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">{amountValue}</div>
                        </div>

                        <div className="hidden justify-between space-x-2 md:flex xl:hidden">
                            <div>{t("pages.statistics.insights.transactions.header.date")}:</div>
                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">{dateFormatted}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="md-lg:pl-16 flex justify-between space-x-2 md:hidden xl:flex xl:w-[330px]">
                <div>{t("pages.statistics.insights.transactions.header.date")}:</div>
                <div className="text-theme-secondary-900 dark:text-theme-dark-50">{dateFormatted}</div>
            </div>
        </div>
    );
}
