import classNames from "classnames";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import Badge from "@/Components/General/Badge";
import Tooltip from "@/Components/General/Tooltip";

export function AddressingGeneric({
    sender,
    senderUsername,
    recipient,
    recipientUsername,
    contractAddress,
    className,
    withTruncate = false,
    disableTooltip = false,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    sender: string;
    senderUsername?: string;
    recipient: string;
    recipientUsername?: string;
    contractAddress: string;
    withTruncate?: boolean;
    disableTooltip?: boolean;
}) {
    const { t } = useTranslation();

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

                <Tooltip content={senderUsername} disabled={!senderUsername} dynamic className="min-w-0 truncate">
                    <Link className="link whitespace-nowrap" href={route("wallet", sender ?? "")}>
                        {senderUsername ? senderUsername : <TruncateMiddle>{sender}</TruncateMiddle>}
                    </Link>
                </Tooltip>
            </div>

            <div className="flex items-center space-x-2">
                <Badge className="encapsulated-badge w-[39px] text-center">{t("tables.transactions.to")}</Badge>

                <Tooltip
                    content={recipientUsername}
                    disabled={!recipientUsername || disableTooltip}
                    dynamic
                    className="min-w-0 truncate"
                >
                    {withTruncate ? (
                        <Link className="link whitespace-nowrap" href={route("wallet", recipient ?? "")}>
                            {recipientUsername ? recipientUsername : <TruncateMiddle>{recipient}</TruncateMiddle>}
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
