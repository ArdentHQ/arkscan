import TruncateMiddle from "../General/TruncateMiddle";
import classNames from '../../utils/class-names';
import { ITransaction } from "@/types";
import CircleMinusSmallIcon from "@ui/icons/circle/minus-small.svg?react";

export default function ID({ transaction, truncate = true, className = '' }: {
    transaction: ITransaction;
    truncate?: boolean | number;
    className?: string;
}) {
    return (
        <div className={classNames({
            "min-w-0": true,
            'flex space-x-2 box-border h-[21px] items-center bg-theme-danger-50 dark:bg-transparent border border-transparent dark:border-theme-failed-state-bg px-1.5 rounded': transaction.status === false,
            [className]: true,
        })}>
            <div className="min-w-0 truncate">
                <a
                    className={classNames({
                        "whitespace-nowrap link": true,
                        "!text-theme-danger-700 dark:!text-theme-failed-state-text": transaction.status === false,
                    })}
                    href={`/addresses/${transaction.hash}`}
                >
                    <>
                        {truncate === true && (
                            <TruncateMiddle length={5}>
                                {transaction.hash}
                            </TruncateMiddle>
                        )}

                        {truncate === false && transaction.hash}
                    </>
                </a>
            </div>

            {transaction.status === false && (
                <div>
                    <CircleMinusSmallIcon className="text-theme-danger-700 dark:text-theme-failed-state-text w-3 h-3" />
                </div>
            )}
        </div>
    );
}
