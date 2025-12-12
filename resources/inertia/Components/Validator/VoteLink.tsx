import { IWallet } from "@/types/generated";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";
import classNames from "classnames";
import ExternalLink from "../General/ExternalLink";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import Dropdown from "../General/Dropdown/Dropdown";
import { useArkConnect } from "@/Providers/ArkConnect/ArkConnectContext";
import useSharedData from "@/hooks/use-shared-data";

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
    const { network } = useSharedData();
    const { hasExtension, isConnected, isOnSameNetwork, performVote, votingForAddress, isArkConnectEnabled } =
        useArkConnect();

    const validatorAddress = wallet.address;

    const shouldRenderDropdown = !wallet.isResigned || votingForAddress === validatorAddress;

    const showArkConnectOption = isArkConnectEnabled && hasExtension;
    const showWrongNetwork = showArkConnectOption && isConnected && isOnSameNetwork === false;
    const showVoteAction = showArkConnectOption && isConnected && isOnSameNetwork === true;

    return (
        <DropdownProvider>
            <>
                {wallet.isResigned && votingForAddress !== validatorAddress && (
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
                )}

                {shouldRenderDropdown && (
                    <Dropdown
                        closeOnClick={false}
                        buttonClass={classNames({
                            "font-semibold hover:underline": true,
                            "text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500 dim:text-theme-dim-blue-600 dim:hover:text-theme-dim-blue-700":
                                votingForAddress !== validatorAddress,
                            "text-theme-danger-400 hover:text-theme-danger-500": votingForAddress === validatorAddress,
                        })}
                        useDefaultButtonClasses={false}
                        button={<>{votingForAddress !== validatorAddress ? voteText : unvoteText}</>}
                        dropdownClasses={classNames({
                            "w-[147px]": true,
                        })}
                        zIndex={20}
                        dropdownContentClasses="bg-white dark:bg-theme-dark-900 dark:border dark:border-theme-dark-800 rounded-xl shadow-lg"
                    >
                        <div className="overflow-hidden rounded-t-xl">
                            <div className="flex bg-theme-secondary-200 px-6 py-2 text-sm font-semibold leading-4.25 dark:bg-theme-dark-950">
                                {t("general.vote_with")}
                            </div>

                            <div className="flex flex-col px-6 py-3">
                                {wallet.voteUrl && (
                                    <ExternalLink
                                        url={wallet.voteUrl}
                                        className="link flex items-center space-x-2 py-3 font-semibold leading-5"
                                    >
                                        {t("brands.arkvault")}
                                    </ExternalLink>
                                )}

                                {showWrongNetwork && (
                                    <Tippy
                                        content={t(`general.arkconnect.wrong_network.${network?.alias ?? "mainnet"}`)}
                                    >
                                        <div>
                                            <button
                                                type="button"
                                                className="flex items-center space-x-2 py-3 font-semibold leading-5 text-theme-secondary-500 dark:text-theme-dark-500"
                                                disabled
                                            >
                                                {t("brands.arkconnect")}
                                            </button>
                                        </div>
                                    </Tippy>
                                )}

                                {showVoteAction && (
                                    <button
                                        type="button"
                                        className="link flex items-center space-x-2 py-3 font-semibold leading-5"
                                        onClick={() => performVote(validatorAddress)}
                                    >
                                        {t("brands.arkconnect")}
                                    </button>
                                )}
                            </div>
                        </div>
                    </Dropdown>
                )}
            </>
        </DropdownProvider>
    );
}
