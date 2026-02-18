import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { formatUnits, parseUnits, weiToArk } from "@/utils/UnitConverter";
import AmountSmall from "@/Components/General/AmountSmall";
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
            return sum + Number(weiToArk(tf.amount));
        }, 0);
        const { value: totalCompact, suffix: totalSuffix } = formatCompact(totalRaw);

        return (
            <PageSection title={t("pages.transaction.tokens_transferred")}>
                <SectionDetailRow title={t("pages.transaction.header.to")} headerWidthClass={headerWidthClass}>
                    <span>
                        Multiple (
                        <a href="#transfer-details" className="link">
                            {transfers.length}
                        </a>
                        )
                    </span>
                </SectionDetailRow>

                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    <span className="inline-flex items-center space-x-1">
                        <AmountSmall amount={totalCompact} hideTooltip hideCurrency suffix={totalSuffix} />
                        <span>{tokenSymbol}</span>
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
    const { value: compactAmount, suffix: compactSuffix } =
        rawAmount !== null ? formatCompact(rawAmount) : { value: 0, suffix: undefined };

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

            {rawAmount !== null && (
                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    <span className="inline-flex items-center space-x-1">
                        <AmountSmall amount={compactAmount} hideTooltip hideCurrency suffix={compactSuffix} />
                        <span>{tokenSymbol}</span>
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
