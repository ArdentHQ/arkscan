import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import Number from "@/Components/General/Number";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";

export default function ViewAllFooter({ total, suffix, href }: { total: number; suffix: string; href: string }) {
    const { t } = useTranslation();

    return (
        <div className="-mx-6 mt-4 flex flex-col items-center space-y-3 rounded-b-xl border-t border-theme-secondary-300 px-6 pt-4 dark:border-theme-dark-700 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 md:mx-0 md:mt-0 md:border md:border-t-0 md:pb-4">
            <div className="font-semibold dark:text-theme-dark-200 sm:mr-8">
                <span>
                    <Number>{total}</Number>
                </span>
                <span>&nbsp;{suffix}</span>
            </div>

            <div className="flex w-full sm:w-auto">
                <Link href={href} className="button-secondary h-8 w-full py-1.5!">
                    <div className="flex items-center justify-center space-x-2">
                        <span>{t("pagination.view_all")}</span>

                        <ChevronRightSmallIcon className="h-3 w-3" />
                    </div>
                </Link>
            </div>
        </div>
    );
}
