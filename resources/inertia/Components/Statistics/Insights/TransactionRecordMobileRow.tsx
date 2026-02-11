import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import Number from "@/Components/General/Number";
import { StatisticsRecord } from "@/Pages/Statistics.contracts";

export default function TransactionRecordMobileRow({
    recordKey,
    record,
}: {
    recordKey: string;
    record: StatisticsRecord | null;
}) {
    const { t } = useTranslation();
    const title = t(`pages.statistics.insights.transactions.header.mobile.${recordKey}`);

    if (!record) {
        return (
            <div className="flex w-full flex-col justify-between space-y-3 pt-3 sm:flex-row sm:space-y-0 md:hidden">
                <div className="flex flex-col space-y-2">
                    <span>{title}</span>
                    <div className="md:w-50">{t("general.na")}</div>
                </div>
            </div>
        );
    }

    const isTransaction = record.type === "transaction";
    const isBlock = record.type === "block";

    const mainValue = (() => {
        if (isTransaction) {
            return record.amount;
        }

        if (recordKey === "most_transactions_in_block" && isBlock) {
            return <Number>{record.transactionCount ?? 0}</Number>;
        }

        if (recordKey === "highest_fee" && isBlock) {
            return record.fee ?? t("general.na");
        }

        return t("general.na");
    })();

    const recordUrl = record.type === "transaction" ? route("transaction", record.hash) : record.url;

    return (
        <div className="flex w-full flex-col justify-between space-y-3 pt-3 sm:flex-row sm:space-y-0 md:hidden">
            <div className="flex flex-col space-y-2">
                <span>{title}</span>
                <Link href={recordUrl} className="link">
                    {mainValue}
                </Link>
            </div>

            <div className="flex w-[90px] flex-col space-y-2">
                <div>{t("pages.statistics.insights.transactions.header.date")}</div>
                <div className="text-theme-secondary-900 dark:text-theme-dark-50">{record.date}</div>
            </div>
        </div>
    );
}
