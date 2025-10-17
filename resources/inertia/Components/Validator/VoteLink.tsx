import { IWallet } from "@/types";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";
import classNames from "@/utils/class-names";
import ExternalLink from "../General/ExternalLink";

// TODO: https://app.clickup.com/t/86dxwp2mj
export default function VoteLink({
    wallet,
    voteText,
    unvoteText,
}: {
    wallet: IWallet;
    voteText: React.ReactNode;
    unvoteText: React.ReactNode;
}) {
    const { t } = useTranslation();

    // @TODO: this is temporal: (I suppose it came from arkconnect?)
    const votingForAddress = null;
    const validatorAddress = wallet.address;

    return (
        <>
            {wallet.isResigned && votingForAddress !== validatorAddress && (
                <Tippy
                    content={t("pages.wallet.validator.resigned_vote_tooltip")}
                >
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
            )}

            <div className="relative">
                <button
                    type="button"
                    className={classNames({
                        "text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500 dim:text-theme-dim-blue-600 dim:hover:text-theme-dim-blue-700":
                            votingForAddress !== validatorAddress,
                        "text-theme-danger-400 hover:text-theme-danger-500":
                            votingForAddress === validatorAddress,
                    })}
                >
                    {votingForAddress !== validatorAddress
                        ? voteText
                        : unvoteText}
                </button>

                <div className="absolute right-0 mt-2 dropdown transition-opacity w-[147px] z-20 shadow-lg rounded-xl bg-white dark:bg-theme-dark-900 dark:border dark:border-theme-dark-800">
                    <div className="flex overflow-y-auto flex-col h-full custom-scroll overscroll-contain">
                        <div className="overflow-hidden rounded-t-xl">
                            <div className="flex py-2 px-6 text-sm font-semibold bg-theme-secondary-200 leading-4.25 dark:bg-theme-dark-950">
                                {t("general.vote_with")}
                            </div>

                            <div className="flex flex-col py-3 px-6">
                                <ExternalLink
                                    url={wallet.voteUrl}
                                    className="flex items-center py-3 space-x-2 font-semibold leading-5 link"
                                >
                                    {t("brands.arkvault")}
                                </ExternalLink>

                                {/* <div
                                            x-show="isConnected &amp;&amp; !isOnSameNetwork"
                                            data-tippy-content="You're connected with a mainnet address. Switch to live.arkscan.io to enable this action."
                                            style="display: none;"
                                        >
                                            <button
                                                type="button"
                                                className="flex items-center py-3 space-x-2 font-semibold leading-5 text-theme-secondary-500 dark:text-theme-dark-500"
                                                disabled
                                                x-on:click="performVote('0xe5a97E663158dEaF3b65bBF88897b8359Dc19F81')"
                                            >
                                                ARKConnect{" "}
                                            </button>
                                        </div>

                                        <div
                                            x-show="!isConnected"
                                            data-tippy-content="Connect Wallet to enable this action."
                                        >
                                            <button
                                                type="button"
                                                className="flex items-center py-3 space-x-2 font-semibold leading-5 text-theme-secondary-500 dark:text-theme-dark-500"
                                                disabled
                                                x-on:click="performVote('0xe5a97E663158dEaF3b65bBF88897b8359Dc19F81')"
                                            >
                                                ARKConnect{" "}
                                            </button>
                                        </div>

                                        <button
                                            x-show="isOnSameNetwork"
                                            type="button"
                                            className="flex items-center py-3 space-x-2 font-semibold leading-5 link"
                                            style="display: none;"
                                        >
                                            ARKConnect{" "}
                                        </button> */}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
