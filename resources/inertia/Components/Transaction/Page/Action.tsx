import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import { ITransaction } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import Method from "@/Components/Transaction/Method";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Badge from "@/Components/General/Badge";
import ContractIcon from "@ui/icons/transaction/contract.svg?react";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import useSharedData from "@/hooks/use-shared-data";
import { weiToArk } from "@/utils/UnitConverter";
import CompactAmount from "@/Components/Tokens/CompactAmount";

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
    const rawAmount = !isUnlimited && tokenApproval.amount !== null ? Number(weiToArk(tokenApproval.amount)) : null;

    let rowTitle = transaction.type;
    if (tokenApproval.isRevoke) {
        rowTitle = t("pages.transaction.approve.revoke");
    }

    return (
        <SectionDetailRow
            title={rowTitle}
            headerWidthClass={headerWidthClass}
            className="!items-start sm:!items-center"
        >
            <div className="flex flex-col items-end gap-y-0.5 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-1 sm:gap-y-1">
                <div className="flex items-center gap-x-1">
                    {tokenApproval.isRevoke ? (
                        <span className="whitespace-nowrap">
                            {t("pages.transaction.approve.removed_permission_for")} {tokenSymbol}{" "}
                            {t("pages.transaction.approve.use_by")}
                        </span>
                    ) : (
                        <>
                            {isUnlimited ? (
                                <span>
                                    {t("general.unlimited")} {tokenSymbol}
                                </span>
                            ) : (
                                rawAmount !== null && <CompactAmount amount={rawAmount} tokenSymbol={tokenSymbol} />
                            )}
                            <span className="whitespace-nowrap">{t("pages.transaction.approve.for_use_by")}</span>
                        </>
                    )}
                </div>

                <div className="flex items-center gap-x-1">
                    <span className="inline-flex items-center">
                        <Link href={route("wallet", tokenApproval.spender)} className="link">
                            <span className="hidden md:inline">
                                {tokenApproval.spenderHasUsername ? (
                                    tokenApproval.spenderUsername
                                ) : (
                                    <TruncateMiddle length={14}>{tokenApproval.spender}</TruncateMiddle>
                                )}
                            </span>
                            <span className="md:hidden">
                                <TruncateMiddle length={14}>{tokenApproval.spender}</TruncateMiddle>
                            </span>
                        </Link>

                        <Badge className="ml-1.5 inline-flex items-center">
                            <ContractIcon className="h-3 w-3" />
                        </Badge>
                    </span>
                </div>

                <div className="flex items-center gap-x-1">
                    <span className="whitespace-nowrap">{t("pages.transaction.approve.on_behalf_of")}</span>

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
