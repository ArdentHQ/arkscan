import { useTranslation } from "react-i18next";
import { ITransaction } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TransactionAddress from "./Address";
import { TransactionDetails } from "@/Pages/Transaction.contracts";

export default function TransactionAddressing({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: ITransaction;
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();

    const recipientAddress =
        transaction.to ?? transaction.deployed_contract_address ?? transaction.from;

    const recipientLabel = details.recipientIsContract
        ? t("pages.transaction.header.interacted_with")
        : t("pages.transaction.header.to");

    return (
        <PageSection title={t("pages.transaction.addressing")}>
            <SectionDetailRow title={t("pages.transaction.header.from")} headerWidthClass={headerWidthClass}>
                <TransactionAddress wallet={transaction.sender} address={transaction.from} />
            </SectionDetailRow>

            <SectionDetailRow title={recipientLabel} valueClassName="min-w-0" headerWidthClass={headerWidthClass}>
                <TransactionAddress
                    wallet={transaction.recipient}
                    address={recipientAddress}
                    isContract={details.recipientIsContract}
                />
            </SectionDetailRow>
        </PageSection>
    );
}
