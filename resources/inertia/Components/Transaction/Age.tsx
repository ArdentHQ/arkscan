import { DATE_TIME_FORMAT } from "@/constants";
import { ITransaction } from "@/types";
import Tippy from "@tippyjs/react";
import dayjs from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(dayjsRelativeTime);

export default function Age({ transaction }: { transaction: ITransaction }) {
    const transactionDate = dayjs(transaction.timestamp * 1000);
    const formattedAge = dayjs().to(transactionDate);

    return (
        <Tippy content={transactionDate.format(DATE_TIME_FORMAT)}>
            <span>{formattedAge}</span>
        </Tippy>
    );
}
