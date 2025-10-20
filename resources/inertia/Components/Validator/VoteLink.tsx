import { IWallet } from "@/types";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";
import classNames from "@/utils/class-names";
import ExternalLink from "../General/ExternalLink";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import Dropdown from "../General/Dropdown/Dropdown";

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

    // TODO: add arkconnect https://app.clickup.com/t/86dy38w6m
    const validatorAddress = wallet.address;
    const votingForAddress = null;
    const isConnected = false;
    const isOnSameNetwork = false;

    return (
        <DropdownProvider>
            <>
                {wallet.isResigned && votingForAddress !== validatorAddress && (
                    <Tippy
                        content={t(
                            "pages.wallet.validator.resigned_vote_tooltip"
                        )}
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

                <Dropdown
                    closeOnClick={false}
                    buttonClass={classNames({
                        "font-semibold hover:underline": true,
                        "text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500 dim:text-theme-dim-blue-600 dim:hover:text-theme-dim-blue-700":
                            votingForAddress !== validatorAddress,
                        "text-theme-danger-400 hover:text-theme-danger-500":
                            votingForAddress === validatorAddress,
                    })}
                    useDefaultButtonClasses={false}
                    button={
                        <>
                            {votingForAddress !== validatorAddress
                                ? voteText
                                : unvoteText}
                        </>
                    }
                    dropdownClasses={classNames({
                        "w-[147px]": true,
                    })}
                    zIndex={20}
                    dropdownContentClasses="bg-white dark:bg-theme-dark-900 rounded-xl shadow-lg dark:shadow-lg-dark"
                >
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

                            {isConnected && !isOnSameNetwork && (
                                <Tippy
                                    content={t(
                                        "general.arkconnect.wrong_network.mainnet"
                                    )}
                                >
                                    <div>
                                        <button
                                            type="button"
                                            className="flex items-center py-3 space-x-2 font-semibold leading-5 text-theme-secondary-500 dark:text-theme-dark-500"
                                            disabled
                                            onClick={() => {
                                                // TODO https://app.clickup.com/t/86dxwp2mj
                                                /*performVote('0xe5a97E663158dEaF3b65bBF88897b8359Dc19F81')*/
                                            }}
                                        >
                                            {t("brands.arkconnect")}
                                        </button>
                                    </div>
                                </Tippy>
                            )}

                            {!isConnected && (
                                <Tippy
                                    content={t(
                                        "general.arkconnect.connect_wallet_tooltip"
                                    )}
                                >
                                    <div>
                                        <button
                                            type="button"
                                            className="flex items-center py-3 space-x-2 font-semibold leading-5 text-theme-secondary-500 dark:text-theme-dark-500"
                                            disabled
                                            onClick={() => {
                                                // TODO https://app.clickup.com/t/86dxwp2mj
                                                /*performVote('0xe5a97E663158dEaF3b65bBF88897b8359Dc19F81')*/
                                            }}
                                        >
                                            {t("brands.arkconnect")}
                                        </button>
                                    </div>
                                </Tippy>
                            )}

                            {isOnSameNetwork && (
                                <button
                                    type="button"
                                    className="flex items-center py-3 space-x-2 font-semibold leading-5 link"
                                >
                                    {t("brands.arkconnect")}
                                </button>
                            )}
                        </div>
                    </div>
                </Dropdown>
            </>
        </DropdownProvider>
    );
}
