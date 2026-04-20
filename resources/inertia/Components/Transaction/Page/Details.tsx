import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import Number from "@/Components/General/Number";
import type {
    TransactionDetails as TransactionDetailsProps,
    TransactionShowProps,
} from "@/Pages/Transaction.contracts";
import { formatDateTime } from "@/utils/formatter";

export default function TransactionDetails({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: TransactionShowProps["transaction"];
    details: TransactionDetailsProps;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();

    const timestamp = formatDateTime(transaction.timestamp);

    return (
        <PageSection title={t("pages.transaction.transaction_details")}>
            <SectionDetailRow
                title={t("pages.transaction.header.timestamp")}
                value={timestamp}
                headerWidthClass={headerWidthClass}
            />

            <SectionDetailRow title={t("pages.transaction.header.block")} headerWidthClass={headerWidthClass}>
                <Link href={route("block", transaction.block_hash)} className="link">
                    <Number>{transaction.block_number}</Number>
                </Link>
            </SectionDetailRow>

            <SectionDetailRow title={t("pages.transaction.header.nonce")} headerWidthClass={headerWidthClass}>
                <Number>{transaction.nonce}</Number>
            </SectionDetailRow>
        </PageSection>
    );
}
