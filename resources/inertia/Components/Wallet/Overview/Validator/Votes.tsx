import { IWallet } from "@/types/generated";
import WalletOverviewItemEntry from "../ItemEntry";
import { useTranslation } from "react-i18next";
import { NetworkCurrency } from "@/Components/General/NetworkCurrency";
import Tooltip from "@/Components/General/Tooltip";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { ITab } from "@/Providers/Tabs/types";

export default function WalletOverviewValidatorVotes({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const tabs = useTabs();

    return (
        <WalletOverviewItemEntry
            title={t("pages.wallet.validator.votes_title")}
            hasEmptyValue={!wallet.isValidator}
            value={
                <>
                    {wallet.isValidator && (
                        <div className="flex items-center space-x-1">
                            <div>
                                <Tooltip content={NetworkCurrency({ value: wallet.votes })}>
                                    <NetworkCurrency value={wallet.votes} decimals={0} />
                                </Tooltip>
                            </div>

                            <button
                                type="button"
                                className="link"
                                onClick={() => {
                                    const scrollToVotersTab = (tab?: ITab) => {
                                        if (tab === undefined || tab.value === "voters") {
                                            const offsetTop = document.getElementById("wallet:tabs:content")!.offsetTop;
                                            const navbarHeight = document.querySelector("#navbar")?.clientHeight ?? 0;

                                            window.scrollTo({
                                                top: offsetTop - navbarHeight,
                                                behavior: "smooth",
                                            });
                                        }

                                        if (tab !== undefined) {
                                            tabs.removeEventListener("tabChange", scrollToVotersTab);
                                        }
                                    };

                                    if (tabs.currentTab === "voters") {
                                        scrollToVotersTab();

                                        return;
                                    }

                                    tabs.addEventListener("tabChange", scrollToVotersTab);
                                    tabs.select("voters");
                                }}
                            >
                                {t("general.view")}
                            </button>
                        </div>
                    )}
                </>
            }
        />
    );
}
