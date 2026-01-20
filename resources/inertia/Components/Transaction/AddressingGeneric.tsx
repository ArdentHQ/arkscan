import { ITransaction } from "@/types/generated";
import classNames from "classnames";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import Badge from "@/Components/General/Badge";
import Tooltip from "@/Components/General/Tooltip";

export default function AddressingGeneric({
    transaction,
    className,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    transaction: ITransaction;
}) {
    const { t } = useTranslation();

    const sender = transaction.sender;
    const senderUsername = sender?.hasUsername ? sender?.username : null;
    const recipient = transaction.recipient;
    const senderHasUsername = sender?.hasUsername ?? false;
    const recipientHasUsername = recipient?.hasUsername ?? false;
    const contractAddress = transaction.deployed_contract_address ?? transaction.to ?? recipient?.address ?? "";

    return (
        <div
            className={classNames(
                "flex flex-col space-y-2 text-sm font-semibold sm:space-y-1 md:space-y-2 md-lg:flex-row md-lg:items-center md-lg:space-x-9 md-lg:space-y-0",
                className,
            )}
            {...props}
        >
            <div className="flex items-center space-x-2 md-lg:w-41">
                <Badge className="encapsulated-badge w-[39px] text-center">{t("tables.transactions.from")}</Badge>

                <Tooltip content={senderUsername} disabled={!senderHasUsername} dynamic className="min-w-0 truncate">
                    <Link className="link whitespace-nowrap" href={route("wallet", sender?.address ?? "")}>
                        {senderHasUsername ? senderUsername : <TruncateMiddle>{sender?.address}</TruncateMiddle>}
                    </Link>
                </Tooltip>
            </div>

            <div className="flex items-center space-x-2">
                <Badge className="encapsulated-badge w-[39px] text-center">{t("tables.transactions.to")}</Badge>

                <Tooltip
                    content={recipient?.username}
                    disabled={!recipientHasUsername || !transaction.isTransfer}
                    dynamic
                    className="min-w-0 truncate"
                >
                    {transaction.isTransfer || transaction.isTokenTransfer ? (
                        <Link className="link whitespace-nowrap" href={route("wallet", recipient?.address ?? "")}>
                            {recipientHasUsername ? (
                                recipient?.username
                            ) : (
                                <TruncateMiddle>{recipient?.address}</TruncateMiddle>
                            )}
                        </Link>
                    ) : contractAddress ? (
                        <Link className="link whitespace-nowrap" href={route("wallet", contractAddress)}>
                            {t("tables.transactions.contract")}
                        </Link>
                    ) : (
                        <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                            {t("tables.transactions.contract")}
                        </span>
                    )}
                </Tooltip>
            </div>
        </div>
    );
}
