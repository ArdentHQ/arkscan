import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { formatUnits, parseUnits } from "@/utils/UnitConverter";
import AmountFiatTooltip from "@/Components/General/AmountFiatTooltip";
import { formatCompact } from "@/utils/number-formatter";

export default function TransactionToken({
    details,
    headerWidthClass,
}: {
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!details.tokenTransfer) {
        return null;
    }

    const tokenSymbol = details.token?.symbol ?? network?.currency;
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
        </PageSection>
    );
}
