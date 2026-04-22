import { useTranslation } from "react-i18next";
import TwitterIcon from "@ui/icons/brands/x.svg?react";
import GitHubIcon from "@ui/icons/brands/solid/github.svg?react";
import Card from "../General/Card";
import SocialSquare, { InformationCardNetwork } from "./SocialSquare";
import { SocialNetworkUrls } from "@/Pages/Support.contracts";

export default function InformationCard({ socialNetworkUrls }: { socialNetworkUrls: SocialNetworkUrls }) {
    const { t } = useTranslation();

    const socialNetworks: InformationCardNetwork[] = [
        {
            url: socialNetworkUrls.twitter,
            icon: TwitterIcon,
        },
        {
            url: socialNetworkUrls.github,
            icon: GitHubIcon,
        },
    ];

    return (
        <div className="flex flex-1 flex-col justify-between rounded-xl border-theme-secondary-300 dark:border-theme-dark-700 md:border lg:mr-1.5 lg:w-1/2">
            <div className="p-6">
                <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-lg">
                    {t("pages.contact.let_us_help.title", { ns: "ui" })}
                </div>

                <div className="paragraph-description mt-2">{t("pages.support.let_us_help.description")}</div>

                <div className="mt-4 flex flex-col space-y-3 sm:flex-row sm:items-center sm:space-x-2 sm:space-y-0">
                    <a
                        href={t("pages.support.docs")}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="button-secondary"
                    >
                        {t("actions.documentation", { ns: "ui" })}
                    </a>
                </div>
            </div>

            <hr className="mx-6 border-theme-secondary-300 dark:border-theme-dark-700" />

            <div className="flex-1 p-6">
                <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-lg">
                    {t("pages.contact.additional_support.title", { ns: "ui" })}
                </div>

                <div className="paragraph-description mt-2">{t("pages.support.additional")}</div>
            </div>

            <hr className="mx-6 border-theme-secondary-300 dark:border-theme-dark-700 md:hidden" />

            <div className="space-y-3 rounded-b-xl p-6 text-theme-secondary-900 dark:text-theme-dark-200 md:bg-theme-secondary-100 dark:md:bg-theme-dark-950">
                <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-lg">
                    {t("pages.contact.social.subtitle", { ns: "ui" })}
                </div>

                <div className="flex space-x-3 text-theme-secondary-700 dark:text-theme-dark-300">
                    {socialNetworks.map(({ url, icon }, index) => (
                        <SocialSquare
                            key={index}
                            className="h-10 w-10 rounded! border border-theme-secondary-300 dark:border-theme-dark-700"
                            hoverClass="hover:bg-theme-secondary-300 hover:text-theme-secondary-900 dark:hover:bg-theme-dark-700 dark:hover:text-theme-dark-50"
                            url={url}
                            icon={icon}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}
