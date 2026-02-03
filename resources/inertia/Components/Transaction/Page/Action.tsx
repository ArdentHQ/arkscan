import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import { ITransaction } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import Method from "@/Components/Transaction/Method";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { TransactionDetails } from "@/Pages/Transaction.contracts";

export default function TransactionAction({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: ITransaction;
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();

    const votedValidator = transaction.votedFor;
    const votedValidatorUsername = transaction.votedForUsername;

    return (
        <PageSection title={t("pages.transaction.action")}>
            <SectionDetailRow
                title={t("pages.transaction.header.method")}
                valueClassName="inline"
                headerWidthClass={headerWidthClass}
            >
                <Method transaction={transaction} />
            </SectionDetailRow>

            {transaction.isVote && votedValidator && (
                <SectionDetailRow title={t("pages.transaction.header.validator")} headerWidthClass={headerWidthClass}>
                    <Link href={route("wallet", votedValidator)} className="link">
                        {votedValidatorUsername ? (
                            <span>{votedValidatorUsername}</span>
                        ) : (
                            <>
                                <span className="hidden md:inline">{votedValidator}</span>
                                <span className="md:hidden">
                                    <TruncateMiddle>{votedValidator}</TruncateMiddle>
                                </span>
                            </>
                        )}
                    </Link>
                </SectionDetailRow>
            )}

            {(transaction.isValidatorRegistration || transaction.isValidatorUpdate) && details.validatorPublicKey && (
                <SectionDetailRow
                    title={t("pages.transaction.header.validator")}
                    valueClassName="min-w-0 overflow-auto max-w-full"
                    headerWidthClass={headerWidthClass}
                >
                    <span className="hidden sm:inline overflow-auto"><TruncateDynamic value={details.validatorPublicKey} /></span>
                    <span className="sm:hidden">
                        <TruncateMiddle>{details.validatorPublicKey}</TruncateMiddle>
                    </span>
                </SectionDetailRow>
            )}

            {transaction.isUsernameRegistration && details.username && (
                <SectionDetailRow
                    title={t("pages.transaction.header.username")}
                    valueClassName="min-w-0"
                    headerWidthClass={headerWidthClass}
                >
                    {details.username}
                </SectionDetailRow>
            )}
        </PageSection>
    );
}
