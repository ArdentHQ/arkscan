import TruncateMiddle from "../General/TruncateMiddle";
import classNames from "classnames";
import { Transaction } from "@/models/Transaction";
import CircleMinusSmallIcon from "@ui/icons/circle/minus-small.svg?react";
import Age from "../Model/Age";
import { Link } from "@inertiajs/react";

export default function ID({ transaction, withoutAge = false }: { transaction: Transaction; withoutAge?: boolean }) {
    return (
        <div className="flex flex-col md:space-y-1 xl:space-y-0">
            <div
                className={classNames({
                    "leading-4.25": true,
                    "bg-theme-danger-50 dark:border-theme-failed-state-bg box-border flex h-[21px] items-center space-x-2 rounded border border-transparent px-1.5 dark:bg-transparent":
                        transaction.hasFailed,
                })}
            >
                <Link
                    href={route("transaction", transaction.hash)}
                    className={classNames({
                        "link mx-auto text-sm leading-4.25 font-semibold whitespace-nowrap": true,
                        "text-theme-danger-700! dark:text-theme-failed-state-text!": transaction.hasFailed,
                    })}
                >
                    <TruncateMiddle>{transaction.hash}</TruncateMiddle>
                </Link>

                {transaction.hasFailed && (
                    <div>
                        <CircleMinusSmallIcon className="text-theme-danger-700 dark:text-theme-failed-state-text h-3 w-3" />
                    </div>
                )}
            </div>

            {!withoutAge && (
                <Age timestamp={transaction.timestamp} className="hidden text-xs leading-3.75 md:block xl:hidden" />
            )}
        </div>
    );
}
