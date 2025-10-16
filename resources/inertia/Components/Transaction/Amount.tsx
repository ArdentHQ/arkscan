import { useConfig } from "@/Providers/Config/ConfigContext";
import { ITransaction } from "@/types";
import classNames from "@/utils/class-names";
import Fee from "./Fee";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

export default function Amount({
    transaction,
    withoutFee = false,
    withNetworkCurrency = false,
    breakpoint = 'md-lg',
}: {
    transaction: ITransaction;
    withoutFee?: boolean;
    withNetworkCurrency?: boolean;
    breakpoint?: 'md-lg' | 'lg' | 'xl';
}) {
    const { network } = useConfig();

    const feeBreakpointClass = ({
        'md-lg': 'md-lg:hidden',
        'lg': 'lg:hidden',
        'xl': 'xl:hidden',
    } as Record<string, string>)[breakpoint];

    const containerBreakpointClass = ({
        'md-lg': 'md-lg:space-y-0',
        'lg': 'lg:space-y-0',
        'xl': 'xl:space-y-0',
    } as Record<string, string>)[breakpoint];

    return (
        <div className={classNames({
            'flex flex-col md:space-y-1': true,
            [containerBreakpointClass]: true,
        })}>
            <div className="inline-block leading-4.25">
                <AmountFiatTooltip
                    amount={transaction.amount}
                    amountForItself={transaction.amountForItself}
                    fiat={transaction.fiat}
                    isSent={transaction.isSent}
                    isReceived={transaction.isReceived}
                    transaction={transaction}
                />

                {withNetworkCurrency && (
                    <span className="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-200">
                        {network!.currency}
                    </span>
                )}
            </div>

            {! withoutFee && (
                <Fee
                    transaction={transaction}
                    className={classNames({
                        'hidden text-xs md:block text-theme-secondary-700 dark:text-theme-dark-200': true,
                        [feeBreakpointClass]: true,
                    })}
                />
            )}
        </div>
    );
}
