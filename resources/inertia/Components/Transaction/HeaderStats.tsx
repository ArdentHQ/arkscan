import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import { useTranslation } from "react-i18next";
import useShareData from "@/hooks/use-shared-data";
import { TransactionsProps } from "@/Pages/Transactions.contracts";
import { currencyWithDecimals } from "@/utils/number-formatter";

export default function HeaderStats({
    transactionCount,
    volume,
    totalFees,
    averageFee,
}: Pick<TransactionsProps["statistics"], "transactionCount" | "volume" | "totalFees" | "averageFee">) {
    const { t } = useTranslation();
    const { network } = useShareData();

    return (
        <div className="grid w-full flex-1 grid-cols-1 gap-2 px-6 pb-6 sm:grid-cols-2 md:mx-auto md:max-w-7xl md:gap-3 md:px-10 md:pb-3 xl:grid-cols-4">
            <Card className="flex-1">
                <Detail title={t("pages.transactions.transactions_24h")}>
                    <Number>{transactionCount}</Number>
                </Detail>
            </Card>
            <Card className="flex-1">
                <Detail title={t("pages.transactions.volume_24h")}>
                    {currencyWithDecimals({ value: volume, currency: network!.currency })}
                </Detail>
            </Card>
            <Card className="flex-1">
                <Detail title={t("pages.transactions.total_fees_24h")}>
                    {currencyWithDecimals({ value: totalFees, currency: network!.currency })}
                </Detail>
            </Card>
            <Card className="flex-1">
                <Detail title={t("pages.transactions.average_fee_24h")}>
                    {currencyWithDecimals({ value: averageFee, currency: network!.currency })}
                </Detail>
            </Card>
        </div>
    );
}
