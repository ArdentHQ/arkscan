import { useTranslation } from "react-i18next";
import Container from "../General/Container";
import Badge from "../General/Badge";
import CircleInfoIcon from "@ui/icons/circle/info.svg?react";
import CompatibleWalletsLearnMore from "./LearnMore";
import arkvaultImage from "@images/wallets/arkvault.svg";
import arkvaultImageDark from "@images/wallets/arkvault-dark.svg";
import arkvaultImageDim from "@images/wallets/arkvault-dim.svg";

export default function ArkVault() {
    const { t } = useTranslation();

    return (
        <Container className="w-full p-0!">
            <div className="md-lg:flex-row flex w-full flex-col">
                <div className="flex flex-1 flex-col justify-center pb-4 sm:pb-8 md:px-8 xl:py-8">
                    <div className="flex">
                        <Badge
                            colors="dark:text-white text-theme-primary-900 bg-theme-primary-100 dark:bg-theme-dark-blue-800! dim:bg-theme-dark-500"
                            className="flex items-center space-x-2 border-0 px-2! py-1!"
                        >
                            <CircleInfoIcon className="h-4 w-4 shrink-0" />

                            <span className="leading-4.25">{t("pages.compatible-wallets.arkvault.disclaimer")}</span>
                        </Badge>
                    </div>

                    <div className="xl:mt-6">
                        <h2 className="text-theme-secondary-900 text-lg font-semibold sm:text-2xl">
                            <span>{t("brands.arkvault")} </span>
                            <span className="text-theme-secondary-500 dark:text-theme-dark-500">
                                ({t("pages.compatible-wallets.arkvault.web_wallet")})
                            </span>
                        </h2>

                        <p className="dark:text-theme-dark-200 mt-2 leading-7">
                            {t("pages.compatible-wallets.arkvault.description")}
                        </p>
                    </div>

                    <CompatibleWalletsLearnMore />
                </div>

                <div className="flex flex-1 grow pt-2 pr-3 md:py-2">
                    <img src={arkvaultImage} className="dark:hidden" alt="ARK Vault - Official ARK Wallet" />
                    <img
                        src={arkvaultImageDark}
                        className="dim:hidden hidden dark:block"
                        alt="ARK Vault - Official ARK Wallet"
                    />
                    <img src={arkvaultImageDim} className="dim:block hidden" alt="ARK Vault - Official ARK Wallet" />
                </div>
            </div>
        </Container>
    );
}
