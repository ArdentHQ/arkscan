import { IWallet } from "@/types/generated";
import WalletOverviewItem from "../Item";
import { useTranslation } from "react-i18next";
import WalletOverviewItemEntry from "../ItemEntry";
import WalletOverviewValidatorRank from "./Rank";
import WalletOverviewValidatorVotes from "./Votes";
import WalletOverviewValidatorProductivity from "./Productivity";
import { NetworkCurrency } from "@/Components/General/NetworkCurrency";
import VoteLink from "@/Components/Validator/VoteLink";

function EmptyWalletOverviewValidator() {
    const { t } = useTranslation();

    return (
        <WalletOverviewItem
            title={t("pages.wallet.validator_info")}
            maskedMessage={t("pages.wallet.validator.not_registered_text")}
            className="hidden md-lg:block"
        >
            <WalletOverviewItemEntry title={t("pages.wallet.validator.rank")} />

            <WalletOverviewItemEntry title={t("pages.wallet.validator.votes_title")} />

            <WalletOverviewItemEntry title={t("pages.wallet.validator.productivity_title")} />

            <WalletOverviewItemEntry title={t("pages.wallet.validator.forged_total")} />
        </WalletOverviewItem>
    );
}

export default function WalletOverviewValidator({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();

    if (!wallet.isValidator) {
        return <EmptyWalletOverviewValidator />;
    }

    return (
        <WalletOverviewItem
            title={t("pages.wallet.validator_info")}
            titleExtra={
                <>
                    {!wallet.isResigned && (
                        <div>
                            <VoteLink
                                wallet={wallet}
                                voteText={
                                    <>
                                        <span className="md:hidden">{t("actions.vote")}</span>

                                        <span className="hidden md:inline">
                                            {t("pages.wallet.validator.vote_for_validator")}
                                        </span>
                                    </>
                                }
                                unvoteText={
                                    <>
                                        <span className="md:hidden">{t("actions.unvote")}</span>

                                        <span className="hidden md:inline">
                                            {t("pages.wallet.validator.unvote_validator")}
                                        </span>
                                    </>
                                }
                            />
                        </div>
                    )}
                </>
            }
        >
            <WalletOverviewValidatorRank wallet={wallet} />

            <WalletOverviewValidatorVotes wallet={wallet} />

            <WalletOverviewValidatorProductivity wallet={wallet} />

            <WalletOverviewItemEntry
                title={t("pages.wallet.validator.forged_total")}
                hasEmptyValue={!wallet.isValidator}
                value={wallet.isValidator && <NetworkCurrency value={wallet.totalForged} decimals={0} />}
                tooltip={wallet.isValidator ? <NetworkCurrency value={wallet.totalForged} /> : undefined}
            />
        </WalletOverviewItem>
    );
}
