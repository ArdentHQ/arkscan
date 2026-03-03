import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import AmountSmall from "@/Components/General/AmountSmall";
import { TableHeaderTooltip } from "@/Components/Tables/Desktop/TableHeader";
import { currency, isFiat, networkCurrency } from "@/utils/number-formatter";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { Transaction } from "@/models/Transaction";

export default function TransactionSummary({
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

    const showAmountRow =
        transaction.method.isTransfer ||
        transaction.method.isMultiPayment ||
        transaction.method.isApprove ||
        transaction.method.isBatchTransfer;
    const showLockedAmount = transaction.method.isValidatorRegistration && transaction.amount > 0;
    const showUnlockedAmount = transaction.method.isValidatorResignation;

    const registrationAmount = transaction.validatorRegistration?.amount ?? null;
    const unlockedAmount =
        registrationAmount !== null && registrationAmount > 0 ? registrationAmount : transaction.amount;

    const isFiatCurrency = isFiat(settings!.currency);
    const isSmallFiatValue = isFiatCurrency && details.totalFiatValue !== null && details.totalFiatValue < 0.01;
    const totalFiat = currency(details.totalFiatValue, settings!.currency, true);

    return (
        <PageSection title={t("pages.transaction.transaction_summary")}>
            {showAmountRow && (
                <SectionDetailRow title={t("pages.transaction.header.amount")} headerWidthClass={headerWidthClass}>
                    <AmountSmall amount={transaction.amount} />
                </SectionDetailRow>
            )}

            {showLockedAmount && (
                <SectionDetailRow
                    title={t("pages.transaction.header.locked_amount")}
                    headerWidthClass={headerWidthClass}
                >
                    <div className="flex items-center justify-end space-x-2 sm:justify-start">
                        <AmountSmall amount={transaction.amount} />

                        <TableHeaderTooltip text={t("pages.transaction.locked_amount_tooltip")} type="question" />
                    </div>
                </SectionDetailRow>
            )}

            {showUnlockedAmount && (
                <SectionDetailRow
                    title={t("pages.transaction.header.unlocked_amount")}
                    headerWidthClass={headerWidthClass}
                >
                    <div className="flex items-center justify-end space-x-2 sm:justify-start">
                        <AmountSmall amount={unlockedAmount} />

                        <TableHeaderTooltip
                            text={
                                registrationAmount !== null && registrationAmount > 0
                                    ? t("pages.transaction.unlocked_amount_tooltip")
                                    : t("pages.transaction.legacy_registration_tooltip")
                            }
                            type="question"
                        />
                    </div>
                </SectionDetailRow>
            )}

            <SectionDetailRow
                title={t("pages.transaction.header.fee")}
                value={networkCurrency(transaction.fee, 8, true)}
                headerWidthClass={headerWidthClass}
            />

            {network?.canBeExchanged && (
                <SectionDetailRow
                    title={t("pages.transaction.header.value")}
                    value={isSmallFiatValue ? `<${currency(0.01, settings!.currency)}` : totalFiat}
                    tooltip={isSmallFiatValue ? (totalFiat ?? undefined) : undefined}
                    headerWidthClass={headerWidthClass}
                />
            )}
        </PageSection>
    );
}
