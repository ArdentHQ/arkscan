import { useTranslation } from "react-i18next";
import dayjs from "dayjs";
import { Link } from "@inertiajs/react";
import { ITransaction } from "@/types/generated";
import { DATE_TIME_FORMAT } from "@/constants";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import Number from "@/Components/General/Number";

export default function TransactionDetails({
    transaction,
    headerWidthClass,
}: {
    transaction: ITransaction;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();

    const timestamp = dayjs(transaction.timestamp * 1000).format(DATE_TIME_FORMAT);

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
