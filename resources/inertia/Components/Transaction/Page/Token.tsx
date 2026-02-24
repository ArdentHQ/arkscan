import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { formatUnits, parseUnits, weiToArk } from "@/utils/UnitConverter";
import { currency } from "@/utils/number-formatter";
import CompactAmount from "@/Components/Tokens/CompactAmount";
import { Transaction } from "@/models/Transaction";

export default function TransactionToken({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: Transaction;
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();
    const { network, settings } = useSharedData();

    const tokenSymbol = details.token?.symbol ?? network?.currency;

    if (transaction.method.isBatchTransfer) {
        const transfers = details.batchTokenTransfers;
        const totalRaw = transfers.reduce((sum, tf) => {
            return sum + Number(weiToArk(tf.amount));
        }, 0);

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
                    <CompactAmount amount={totalRaw} tokenSymbol={tokenSymbol} showFullOnDesktop />
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

    return (
        <PageSection title={t("pages.transaction.tokens_transferred")}>
            <SectionDetailRow title={t("pages.transaction.header.to")} headerWidthClass={headerWidthClass}>
                <TransactionAddress
                    address={tokenTransfer.recipient.address}
                    wallet={{
                        address: tokenTransfer.recipient.address,
                        username: tokenTransfer.recipient.username ?? null,
                    }}
                />
            </SectionDetailRow>

            {rawAmount !== null && (
                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    <CompactAmount amount={rawAmount} tokenSymbol={tokenSymbol} showFullOnDesktop />
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
