import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { weiToArk } from "@/utils/UnitConverter";

export default function TransactionTokenApproval({
    details,
    headerWidthClass,
}: {
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!details.tokenApproval) {
        return null;
    }

    const tokenSymbol = details.token?.symbol ?? network?.currency;
    const tokenApproval = details.tokenApproval;

    const amount = tokenApproval.isUnlimited
        ? t("general.unlimited")
        : tokenApproval.amount !== null
          ? weiToArk(tokenApproval.amount, tokenSymbol)
          : null;

    return (
        <PageSection title={t("pages.transaction.token_approval")}>
            <SectionDetailRow title={t("pages.transaction.header.spender")} headerWidthClass={headerWidthClass}>
                <TransactionAddress
                    address={tokenApproval.spender}
                    wallet={{
                        address: tokenApproval.spender,
                        hasUsername: tokenApproval.spenderHasUsername,
                        username: tokenApproval.spenderUsername,
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
