import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { formatUnits, weiToArk } from "@/utils/UnitConverter";
import { currency } from "@/utils/number-formatter";
import CompactAmount from "@/Components/Tokens/CompactAmount";
import { Transaction } from "@/models/Transaction";
import BigNumber from "bignumber.js";

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
    const { network } = useSharedData();
    const { currency: selectedCurrency } = useSettings();

    const tokenSymbol = details.token?.symbol ?? network?.currency;

    if (transaction.method.isBatchTransfer) {
        const transfers = details.batchTokenTransfers;
        const totalRaw = transfers.reduce((sum, tf) => {
            return sum.plus(weiToArk(tf.amount));
        }, new BigNumber(0));

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
                    <CompactAmount
                        amount={totalRaw.toFixed()}
                        tokenSymbol={tokenSymbol}
                        fullTokenSymbol={details.token?.symbolFull}
                        showFullOnDesktop
                    />
                </SectionDetailRow>

                {network?.canBeExchanged && (
                    <SectionDetailRow
                        title={t("pages.transaction.header.value")}
                        value={currency(0, selectedCurrency)}
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

    const rawAmount = tokenTransfer.amount !== null ? formatUnits(tokenTransfer.amount, "ark") : null;

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
                    value={currency(0, selectedCurrency)}
                    headerWidthClass={headerWidthClass}
                />
            )}
        </PageSection>
    );
}
