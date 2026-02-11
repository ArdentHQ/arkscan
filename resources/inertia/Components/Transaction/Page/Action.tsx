import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import { ITransaction } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import Method from "@/Components/Transaction/Method";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Clipboard from "@/Components/General/Clipboard";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import useSharedData from "@/hooks/use-shared-data";
import { weiToArk } from "@/utils/UnitConverter";

function ApproveActionRow({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: ITransaction;
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    const tokenApproval = details.tokenApproval;
    if (!tokenApproval) {
        return (
            <SectionDetailRow
                title={t("pages.transaction.header.method")}
                valueClassName="inline"
                headerWidthClass={headerWidthClass}
            >
                <Method transaction={transaction} />
            </SectionDetailRow>
        );
    }

    const tokenSymbol = details.token?.symbol ?? network?.currency;

    const isUnlimited = tokenApproval.isUnlimited;
    const amount = !isUnlimited && tokenApproval.amount !== null ? weiToArk(tokenApproval.amount, tokenSymbol) : null;

    const rowTitle = isUnlimited ? `${transaction.type} ${t("general.unlimited")}` : transaction.type;
    const approveHeaderWidth = isUnlimited ? "sm:w-[144px]" : headerWidthClass;

    return (
        <SectionDetailRow title={rowTitle} headerWidthClass={approveHeaderWidth}>
            <div className="flex flex-wrap items-center gap-x-1.5 gap-y-1">
                {amount !== null && <span>{amount}</span>}

                <span className="whitespace-nowrap">{t("pages.transaction.approve.for_trade")}</span>

                <span className="inline-flex items-center">
                    <Link href={route("wallet", tokenApproval.spender)} className="link">
                        <span className="hidden md:inline">
                            {tokenApproval.spenderHasUsername ? (
                                tokenApproval.spenderUsername
                            ) : (
                                <TruncateMiddle>{tokenApproval.spender}</TruncateMiddle>
                            )}
                        </span>
                        <span className="md:hidden">
                            <TruncateMiddle>{tokenApproval.spender}</TruncateMiddle>
                        </span>
                    </Link>

                    <Clipboard
                        value={tokenApproval.spender}
                        noStyling
                        className="transition-default ml-1 flex h-auto w-auto items-center text-theme-secondary-700 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:text-theme-dark-50"
                        tooltipContent={t("pages.wallet.address_copied")}
                        checkmarksClass=""
                    />
                </span>

                <span className="whitespace-nowrap">{t("pages.transaction.approve.by")}</span>

                <Link href={route("wallet", transaction.from)} className="link">
                    <span className="hidden md:inline">
                        {transaction.sender?.hasUsername ? (
                            transaction.sender.username
                        ) : (
                            <TruncateMiddle>{transaction.from}</TruncateMiddle>
                        )}
                    </span>
                    <span className="md:hidden">
                        <TruncateMiddle>{transaction.from}</TruncateMiddle>
                    </span>
                </Link>
            </div>
        </SectionDetailRow>
    );
}

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
            {transaction.isApprove ? (
                <ApproveActionRow transaction={transaction} details={details} headerWidthClass={headerWidthClass} />
            ) : (
                <SectionDetailRow
                    title={t("pages.transaction.header.method")}
                    valueClassName="inline"
                    headerWidthClass={headerWidthClass}
                >
                    <Method transaction={transaction} />
                </SectionDetailRow>
            )}

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
                    valueClassName="min-w-0 overflow-x-auto max-w-full"
                    headerWidthClass={headerWidthClass}
                >
                    <span className="hidden overflow-x-auto sm:inline">
                        <TruncateDynamic value={details.validatorPublicKey} />
                    </span>
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
