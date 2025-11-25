import NavbarTop from "./NavbarTop";
import NavbarDesktop from "./NavbarDesktop";
import { useMemo } from "react";
import { useTranslation } from "react-i18next";
import useShareData from "@/hooks/use-shared-data";

export default function Navbar() {
    const {
        network: { canBeExchanged },
        supportEnabled,
    } = useShareData();

    const { t } = useTranslation();

    const navigation = useMemo(() => {
        return [
            { route: "home", label: t("menus.home") },
            {
                label: t("menus.blockchain"),
                children: [
                    { route: "blocks", label: t("menus.blocks") },
                    { route: "transactions", label: t("menus.transactions") },
                    { route: "validators", label: t("menus.validators") },
                    { route: "top-accounts", label: t("menus.top_accounts") },
                    { route: "statistics", label: t("menus.statistics") },
                ],
            },
            {
                label: t("menus.resources"),
                children: [
                    { route: "validator-monitor", label: t("menus.validator_monitor") },
                    { route: "compatible-wallets", label: t("menus.wallets") },
                    ...(canBeExchanged ? [{ route: "exchanges", label: t("menus.exchanges") }] : []),
                ],
            },
            {
                label: t("menus.developers"),
                children: [
                    { url: t("urls.docs.arkscan"), label: t("menus.docs") },
                    { url: t("urls.docs.api"), label: t("menus.api") },
                    { url: t("urls.github"), label: t("menus.github") },
                    ...(supportEnabled ? [{ route: "contact", label: t("menus.support") }] : []),
                ],
            },
        ];
    }, [t, canBeExchanged, supportEnabled]);

    return (
        <div id="navbar" className="z-30 pb-13 sm:pb-16 md:sticky md:top-0 md:pb-0">
            <NavbarTop />

            <NavbarDesktop />

            {/* @TODO: add mobile navigation (https://app.clickup.com/t/86dyeejm5) */}
        </div>
    );
}
