import useShareData from "@/hooks/use-shared-data";
import Tooltip from "@/Components/General/Tooltip";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import Logo from "@images/logo.svg?react";

export default function NavbarLogo() {
    const {
        navbarName,
        navbarTag,
        network: { currency },
    } = useShareData();
    const { t } = useTranslation();

    return (
        <span className="relative flex items-center">
            <div className="flex space-x-0.5">
                <Logo
                    className={classNames({
                        "bg-theme-danger-400 h-8": true,
                        "rounded-l": navbarTag !== null,
                        rounded: navbarTag === null,
                    })}
                />

                {navbarTag && (
                    <div className="bg-theme-danger-100 text-theme-danger-600 dark:bg-theme-dark-800 dark:text-theme-dark-200 flex rounded-r">
                        <Tooltip content={t("general.navbar.release_tag_tooltip", { tag: navbarTag })}>
                            <div className="flex h-full items-center px-2 text-xs leading-none font-semibold uppercase">
                                {navbarTag}
                            </div>
                        </Tooltip>
                    </div>
                )}
            </div>

            <span className="text-theme-secondary-900 dark:text-theme-dark-50 ml-4 hidden md:flex md:items-center">
                <span className="inline-flex text-lg">
                    <span className="font-bold">{navbarName ? navbarName : currency}</span>

                    <span className="uppercase">{t("generic.scan")}</span>
                </span>
            </span>
        </span>
    );
}
