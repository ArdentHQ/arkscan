import useSharedData from "@/hooks/use-shared-data";
import { ITransaction } from "@/types/generated";
import classNames from "@/utils/class-names";
import Fee from "./Fee";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

export default function Amount({
    transaction,
    withoutFee = false,
    withNetworkCurrency = false,
    breakpoint = "md-lg",
    hideCurrency,
}: {
    transaction: ITransaction;
    withoutFee?: boolean;
    withNetworkCurrency?: boolean;
    breakpoint?: "md-lg" | "lg" | "xl";
    hideCurrency?: boolean;
}) {
    const { network } = useSharedData();

    let isReceived = !transaction.isSent;
    let isSent = transaction.isSent;

    let amount = transaction.amount;
    let amountFiat = transaction.amountFiat;
    let amountForItself: number | undefined = undefined;

    if (isReceived || transaction.isSentToSelf) {
        amount = transaction.amountReceived;
        amountFiat = transaction.amountReceivedFiat;
    } else {
        amountForItself = transaction.amountForItself;
        if (amountForItself > 0) {
            amount = transaction.amountExcludingItself;
        }
    }

    if (transaction.isValidatorResignation) {
        const registration = transaction.validatorRegistration;
        if (registration !== null) {
            amount = registration.amount;
        }

        isReceived = true;
        isSent = false;
    }

    const feeBreakpointClass = (
        {
            "md-lg": "md-lg:hidden",
            lg: "lg:hidden",
            xl: "xl:hidden",
        } as Record<string, string>
    )[breakpoint];

    const containerBreakpointClass = (
        {
            "md-lg": "md-lg:space-y-0",
            lg: "lg:space-y-0",
            xl: "xl:space-y-0",
        } as Record<string, string>
    )[breakpoint];

    return (
        <div
            className={classNames({
                "flex flex-col md:space-y-1": true,
                [containerBreakpointClass]: true,
            })}
        >
            <div className="inline-block leading-4.25">
                <AmountFiatTooltip
                    amount={amount}
                    amountForItself={amountForItself}
                    fiat={amountFiat}
                    isSent={isSent}
                    isReceived={isReceived}
                    transaction={transaction}
                    hideCurrency={hideCurrency}
                />

                {withNetworkCurrency && (
                    <span className="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-200">
                        {network!.currency}
                    </span>
                )}
            </div>

            {!withoutFee && (
                <Fee
                    transaction={transaction}
                    className={classNames({
                        "hidden text-xs md:block": true,
                        [feeBreakpointClass]: true,
                    })}
                    withoutStyling
                />
            )}
        </div>
    );
}
