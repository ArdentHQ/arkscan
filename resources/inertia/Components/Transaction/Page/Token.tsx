import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { weiToArk } from "@/utils/UnitConverter";

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

    const amount = tokenTransfer.amount !== null ? weiToArk(tokenTransfer.amount, tokenSymbol) : null;

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

            {amount !== null && (
                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    {amount}
                </SectionDetailRow>
            )}
        </PageSection>
    );
}
