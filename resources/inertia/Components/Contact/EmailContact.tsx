import { useTranslation } from "react-i18next";
import PaperPlaneIcon from "@ui/icons/paper-plane.svg?react";
import Clipboard from "@/Components/General/Clipboard";

export default function EmailContact({ email }: { email: string }) {
    const { t } = useTranslation();

    return (
        <div className="border-theme-secondary-300 dark:border-theme-dark-700 flex flex-1 flex-col rounded-xl p-6 md:mt-3 md:border lg:mt-0 lg:ml-1.5">
            <div className="text-theme-secondary-900 dark:text-theme-dark-50 mb-2 leading-5.25 font-semibold md:text-lg">
                {t("pages.contact.additional_support.title", { ns: "ui" })}
            </div>

            <div className="paragraph-description dark:text-theme-dark-300">{t("pages.support.additional")}</div>

            <div className="border-theme-secondary-300 bg-theme-secondary-100 dark:border-theme-dark-700 dark:bg-theme-dark-950 mt-4 flex h-[45px] items-center justify-between space-x-3 rounded border px-4 md:mt-6 md:h-14">
                <div className="text-theme-secondary-700 dark:text-theme-dark-300 flex min-w-0 items-center space-x-2">
                    <PaperPlaneIcon className="h-4 w-4 flex-shrink-0" />
                    <span className="truncate text-lg font-semibold">{email}</span>
                </div>

                <Clipboard
                    value={email}
                    wrapperClass="hidden md:block"
                    className="button-secondary flex flex-shrink-0 items-center md:h-8 md:space-x-2 md:px-4 md:py-1.5"
                    noStyling
                    checkmarksClass=""
                >
                    <span>{t("actions.copy")}</span>
                </Clipboard>

                <Clipboard
                    value={email}
                    wrapperClass="md:hidden"
                    className="flex flex-shrink-0 items-center md:h-8 md:space-x-2 md:px-4 md:py-1.5"
                    noStyling
                    checkmarksClass=""
                />
            </div>

            <a
                href={`mailto:${email}`}
                className="button-primary bg-theme-blue-600 dark:!bg-theme-dark-blue-500 mt-4 block w-full text-center"
            >
                {t("pages.support.send_email")}
            </a>

            <p className="text-theme-secondary-500 dark:text-theme-dark-500 mt-2 text-xs leading-3.75 font-semibold">
                {t("pages.support.email_hint")}
            </p>
        </div>
    );
}
