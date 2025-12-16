import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import { useTranslation } from "react-i18next";
import Currency from "@/Components/General/Currency";
import useShareData from "@/hooks/use-shared-data";
import { TransactionsProps } from "@/Pages/Transactions.contracts";

export default function HeaderStats({
    transactionCount,
    volume,
    totalFees,
    averageFee,
}: Pick<TransactionsProps, "transactionCount" | "volume" | "totalFees" | "averageFee">) {
    const { t } = useTranslation();
    const { network } = useShareData();

    return (
        <div className="flex flex-col space-y-2 px-6 pb-6 sm:space-y-3 md:mx-auto md:max-w-7xl md:px-10 xl:flex-row xl:space-x-3 xl:space-y-0">
            <div className="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 md:gap-3 xl:grid-cols-4">
                <Card className="flex-1">
                    <Detail title={t("pages.transactions.transactions_24h")}>
                        <Number>{transactionCount}</Number>
                    </Detail>
                </Card>
                <Card className="flex-1">
                    <Detail title={t("pages.transactions.volume_24h")}>
                        <Currency currency={network!.currency} decimals={0} value={volume} />
                    </Detail>
                </Card>
                <Card className="flex-1">
                    <Detail title={t("pages.transactions.total_fees_24h")}>
                        <Currency currency={network!.currency} decimals={0} value={totalFees} />
                    </Detail>
                </Card>
                <Card className="flex-1">
                    <Detail title={t("pages.transactions.average_fee_24h")}>
                        <Currency currency={network!.currency} decimals={0} value={averageFee} />
                    </Detail>
                </Card>
            </div>
        </div>
    );
}
