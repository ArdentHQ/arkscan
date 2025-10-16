import { DATE_TIME_FORMAT } from "@/constants";
import { ITransaction } from "@/types";
import classNames from "@/utils/class-names";
import Tippy from "@tippyjs/react";
import dayjs from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(dayjsRelativeTime);

export default function Age({
    transaction,
    className = 'text-theme-secondary-900 dark:text-theme-dark-50',
}: {
    transaction: ITransaction;
    className?: string;
}) {
    const transactionDate = dayjs(transaction.timestamp * 1000);
    const formattedAge = dayjs().to(transactionDate);

    return (
        <Tippy content={transactionDate.format(DATE_TIME_FORMAT)}>
            <span className={classNames({
                'text-sm font-semibold leading-4.25': true,
                [className]: true,
            })}>
                {formattedAge}
            </span>
        </Tippy>
    );
}
