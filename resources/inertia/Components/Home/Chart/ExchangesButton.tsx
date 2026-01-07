import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import classNames from "classnames";
import useSharedData from "@/hooks/use-shared-data";

export default function ExchangesButton({
    className = "",
    buttonClassName = "",
}: {
    className?: string;
    buttonClassName?: string;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!network?.canBeExchanged) {
        return null;
    }

    return (
        <div className={className}>
            <Link
                href={route("exchanges")}
                className={classNames("button button-secondary px-4 py-1.5", buttonClassName)}
            >
                <div className="inline-flex items-center space-x-2 lg:space-x-0 xl:space-x-2">
                    <span>{t("actions.exchanges")}</span>

                    <ChevronRightSmallIcon className="lg:hidden xl:block h-3 w-3" />
                </div>
            </Link>
        </div>
    );
}
