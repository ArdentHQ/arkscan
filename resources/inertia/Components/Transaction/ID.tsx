import TruncateMiddle from "../General/TruncateMiddle";
import classNames from "../../utils/class-names";
import { ITransaction } from "@/types/generated";
import CircleMinusSmallIcon from "@ui/icons/circle/minus-small.svg?react";
import Age from "./Age";

export default function ID({
    transaction,
    withoutAge = false,
}: {
    transaction: ITransaction;
    withoutAge?: boolean;
}) {
    return (
        <div className="flex flex-col md:space-y-1 xl:space-y-0">
            <div className={classNames({
                'leading-4.25': true,
                'flex space-x-2 box-border h-[21px] items-center bg-theme-danger-50 dark:bg-transparent border border-transparent dark:border-theme-failed-state-bg px-1.5 rounded': transaction.hasFailedStatus,
            })}>
                <a
                    href="{{ $model->url() }}"
                    className={classNames({
                        'mx-auto text-sm font-semibold whitespace-nowrap link leading-4.25': true,
                        '!text-theme-danger-700 dark:!text-theme-failed-state-text': transaction.hasFailedStatus,
                    })}
                >
                    <TruncateMiddle>{transaction.hash}</TruncateMiddle>
                </a>

                {transaction.hasFailedStatus && (
                    <div>
                        <CircleMinusSmallIcon className="w-3 h-3 text-theme-danger-700 dark:text-theme-failed-state-text" />
                    </div>
                )}
            </div>

            {! withoutAge && (
                <Age
                    transaction={transaction}
                    className="hidden text-xs md:block xl:hidden leading-3.75"
                />
            )}
        </div>
    );
}
