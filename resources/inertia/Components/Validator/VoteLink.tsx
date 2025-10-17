import { IWallet } from "@/types";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";

// TODO: https://app.clickup.com/t/86dxwp2mj
export default function VoteLink({
    wallet,
    voteText,
    unvoteText,
    buttonClass,
}: {
    wallet: IWallet;
    voteText: React.ReactNode;
    unvoteText: React.ReactNode;
    buttonClass?: string;
}) {
    const { t } = useTranslation();

    if (wallet.isResigned) {
        return (
            <Tippy content={t("pages.wallet.validator.resigned_vote_tooltip")}>
                <div>
                    <button
                        type="button"
                        className="text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-500"
                        disabled
                    >
                        {t("actions.vote")}
                    </button>
                </div>
            </Tippy>
        );
    }

    return <></>;
}
