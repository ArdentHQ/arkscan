import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { formatUnits, parseUnits } from "@/utils/UnitConverter";
import AmountFiatTooltip from "@/Components/General/AmountFiatTooltip";
import { currency, formatCompact } from "@/utils/number-formatter";
import { ITransaction } from "@/types/generated";

export default function TransactionToken({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: ITransaction;
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();
    const { network, settings } = useSharedData();

    const tokenSymbol = details.token?.symbol ?? network?.currency;

    if (transaction.isBatchTransfer) {
        const transfers = details.batchTokenTransfers;
        const totalRaw = transfers.reduce((sum, tf) => {
            return sum + Number(formatUnits(parseUnits(tf.amount, "wei"), "ark"));
        }, 0);

        const compact = formatCompact(totalRaw);

        return (
            <PageSection title={t("pages.transaction.tokens_transferred")}>
                <SectionDetailRow title={t("pages.transaction.header.to")} headerWidthClass={headerWidthClass}>
                    <a href="#transfer-details" className="link">
                        {t("pages.transaction.multiple_count", { count: transfers.length })}
                    </a>
                </SectionDetailRow>

                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    <span className="inline-flex items-center space-x-1">
                        <span className="text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                            {compact.value}
                            {compact.suffix}
                        </span>

                        <span className="text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                            {tokenSymbol}
                        </span>
                    </span>
                </SectionDetailRow>

                {network?.canBeExchanged && (
                    <SectionDetailRow
                        title={t("pages.transaction.header.value")}
                        value={currency(0, settings!.currency)}
                        headerWidthClass={headerWidthClass}
                    />
                )}
            </PageSection>
        );
    }

    if (!details.tokenTransfer) {
        return null;
    }

    const tokenTransfer = details.tokenTransfer;

    const rawAmount =
        tokenTransfer.amount !== null ? Number(formatUnits(parseUnits(tokenTransfer.amount, "wei"), "ark")) : null;

    const compact = rawAmount !== null ? formatCompact(rawAmount) : null;

    return (
        <PageSection title={t("pages.transaction.tokens_transferred")}>
            <SectionDetailRow title={t("pages.transaction.header.to")} headerWidthClass={headerWidthClass}>
                <TransactionAddress
                    address={tokenTransfer.recipient}
                    wallet={{
                        address: tokenTransfer.recipient,
                        hasUsername: tokenTransfer.recipientHasUsername,
                        username: tokenTransfer.recipientUsername,
                    }}
                />
            </SectionDetailRow>

            {compact !== null && (
                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    <span className="inline-flex items-center space-x-1">
                        <AmountFiatTooltip amount={compact.value} suffix={compact.suffix} isSent hideCurrency />

                        <span className="text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                            {tokenSymbol}
                        </span>
                    </span>
                </SectionDetailRow>
            )}

            {network?.canBeExchanged && (
                <SectionDetailRow
                    title={t("pages.transaction.header.value")}
                    value={currency(0, settings!.currency)}
                    headerWidthClass={headerWidthClass}
                />
            )}
        </PageSection>
    );
}
