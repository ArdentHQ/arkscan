import { ITransaction } from "@/types/generated";
import { currency, networkCurrency } from "@/utils/number-formatter";
import HintSmallIcon from "@ui/icons/hint-small.svg?react";
import { useTranslation } from "react-i18next";
import AmountSmall from "./AmountSmall";
import Tooltip from "./Tooltip";
import useConfig from "@/hooks/use-config";

function AmountOutput({
    transaction,
    isSent,
    isReceived,
    isSentToSelf,
    amount,
    hideCurrency = false,
}: {
    transaction?: ITransaction;
    isSent: boolean;
    isReceived: boolean;
    isSentToSelf: boolean;
    amount: string | number;
    hideCurrency?: boolean;
}) {
    return (
        <span>
            <span>{isSent && !isSentToSelf ? "- " : isReceived ? "+ " : ""}</span>

            {typeof amount === "number" ? (
                transaction ? (
                    <AmountSmall amount={amount} hideTooltip hideCurrency={hideCurrency} />
                ) : (
                    <span>{networkCurrency(amount)}</span>
                )
            ) : (
                <span>{amount}</span>
            )}
        </span>
    );
}

export default function AmountFiatTooltip({
    transaction,
    isSent = false,
    isReceived = false,
    amount,
    amountForItself,
    fiat,
    className = "text-sm",
    withoutStyling = false,
    hideCurrency = false,
}: {
    transaction?: ITransaction;
    isSent?: boolean;
    isReceived?: boolean;
    amount: string | number;
    amountForItself?: number;
    fiat?: string | number;
    className?: string;
    withoutStyling?: boolean;
    hideCurrency?: boolean;
}) {
    const { t } = useTranslation();
    const { network } = useConfig();

    const classes: string[] = ["inline-flex items-center font-semibold", className];

    let isSentToSelf = typeof amountForItself === "number" && amountForItself > 0;

    let sent = isSent;

    if (!withoutStyling) {
        if (!sent && !isReceived) {
            classes.push("text-theme-secondary-900 dark:text-theme-dark-50");
        }

        if (sent || isReceived) {
            classes.push("flex whitespace-nowrap rounded border");

            if (isSentToSelf) {
                classes.push("pr-1.5");
            } else {
                classes.push("px-1.5 py-0.5");
            }
        }

        if (transaction && transaction.isSentToSelf) {
            classes.push(
                "fiat-tooltip-sent text-theme-secondary-700 bg-theme-secondary-200 border-theme-secondary-200 dark:bg-transparent",
                "dark:border-theme-dark-700 dark:text-theme-dark-200 dim:border-theme-dim-700 dim:text-theme-dim-200 encapsulated-badge",
            );

            sent = false;
            isSentToSelf = true;
        } else {
            if (sent) {
                classes.push(
                    "fiat-tooltip-sent text-theme-orange-dark bg-theme-orange-light border-theme-orange-light dark:bg-transparent",
                );
                classes.push(
                    "dark:border-theme-failed-state-bg dim:border-theme-failed-state-bg dark:text-theme-failed-state-text dim:text-theme-failed-state-text",
                );
            }

            if (isReceived) {
                classes.push(
                    "fiat-tooltip-received text-theme-success-700 bg-theme-success-100 border-theme-success-100",
                    "dark:bg-transparent dark:border-theme-success-700 dark:text-theme-success-500",
                );
            }
        }
    }

    return (
        <span className={classes.join(" ")}>
            {amountForItself !== undefined && amountForItself > 0 && (
                <Tooltip
                    content={t("general.fiat_excluding_self", {
                        amount: currency(amountForItself, network!.currency),
                    })}
                >
                    <div className="mr-1.5 flex h-full items-center bg-[#F6DFB5] px-1.5 py-[4.5px] text-theme-orange-dark dim:bg-theme-failed-state-bg dark:bg-theme-failed-state-bg dark:text-theme-dark-50">
                        <HintSmallIcon className="h-3 w-3" />
                    </div>
                </Tooltip>
            )}

            {fiat && (
                <Tooltip content={fiat}>
                    <AmountOutput
                        transaction={transaction}
                        isSent={isSent}
                        isReceived={isReceived}
                        isSentToSelf={isSentToSelf}
                        amount={amount}
                        hideCurrency={hideCurrency}
                    />
                </Tooltip>
            )}

            {!fiat && (
                <AmountOutput
                    transaction={transaction}
                    isSent={isSent}
                    isReceived={isReceived}
                    isSentToSelf={isSentToSelf}
                    amount={amount}
                    hideCurrency={hideCurrency}
                />
            )}
        </span>
    );
}
