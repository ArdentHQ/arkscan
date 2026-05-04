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
        <div className="border-theme-secondary-300 dark:border-theme-dark-700 flex flex-1 flex-col justify-between rounded-xl md:border lg:mr-1.5 lg:w-1/2">
            <div className="p-6">
                <div className="text-theme-secondary-900 dark:text-theme-dark-50 font-semibold md:text-lg">
                    {t("pages.contact.let_us_help.title", { ns: "ui" })}
                </div>

                <div className="paragraph-description mt-2">{t("pages.support.let_us_help.description")}</div>

                <div className="mt-4 flex flex-col space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">
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

            <hr className="border-theme-secondary-300 dark:border-theme-dark-700 mx-6" />

            <div className="flex-1 p-6">
                <div className="text-theme-secondary-900 dark:text-theme-dark-50 font-semibold md:text-lg">
                    {t("pages.contact.additional_support.title", { ns: "ui" })}
                </div>

                <div className="paragraph-description mt-2">{t("pages.support.additional")}</div>
            </div>

            <hr className="border-theme-secondary-300 dark:border-theme-dark-700 mx-6 md:hidden" />

            <div className="text-theme-secondary-900 dark:text-theme-dark-200 md:bg-theme-secondary-100 dark:md:bg-theme-dark-950 space-y-3 rounded-b-xl p-6">
                <div className="text-theme-secondary-900 dark:text-theme-dark-50 font-semibold md:text-lg">
                    {t("pages.contact.social.subtitle", { ns: "ui" })}
                </div>

                <div className="text-theme-secondary-700 dark:text-theme-dark-300 flex space-x-3">
                    {socialNetworks.map(({ url, icon }, index) => (
                        <SocialSquare
                            key={index}
                            className="border-theme-secondary-300 dark:border-theme-dark-700 h-10 w-10 rounded! border"
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
