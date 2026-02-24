import useSharedData from "@/hooks/use-shared-data";
import classNames from "classnames";
import Fee from "./Fee";
import AmountFiatTooltip from "../General/AmountFiatTooltip";
import { Transaction } from "@/models/Transaction";
import { IWallet } from "@/types/generated";

export default function Amount({
    wallet,
    transaction,
    withoutFee = false,
    withNetworkCurrency = false,
    breakpoint = "md-lg",
    hideCurrency,
    testId,
}: {
    wallet?: IWallet;
    transaction: Transaction;
    withoutFee?: boolean;
    withNetworkCurrency?: boolean;
    breakpoint?: "md-lg" | "lg" | "xl";
    hideCurrency?: boolean;
    testId?: string;
}) {
    const { network } = useSharedData();

    let isSent = false;
    let isReceived = false;

    if (wallet && transaction.isSent(wallet.address)) {
        isSent = true;
    } else if (wallet) {
        isReceived = true;
    }

    let amount = transaction.amount;
    let amountFiat = transaction.amountFiat;
    let amountForItself: number | undefined = undefined;

    if (wallet) {
        if (isReceived || transaction.isSentToSelf(wallet.address)) {
            amount = transaction.amountReceived(wallet.address);
            amountFiat = transaction.amountReceivedFiat;
        } else {
            amountForItself = transaction.amountForItself;
            if (amountForItself > 0) {
                amount = transaction.amountExcludingItself;
            }
        }
    }

    if (transaction.method.isValidatorResignation) {
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
            data-testid={testId}
        >
            <div className="inline-block leading-4.25">
                <AmountFiatTooltip
                    amount={amount}
                    amountForItself={amountForItself}
                    fiat={amountFiat}
                    isSent={isSent}
                    isSentToSelf={wallet ? transaction.isSentToSelf(wallet.address) : false}
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
