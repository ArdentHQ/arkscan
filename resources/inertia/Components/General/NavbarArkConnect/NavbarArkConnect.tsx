import { useArkConnect } from "@/Providers/ArkConnect/ArkConnectContext";
import classNames from "classnames";
import { useCallback } from "react";
import { useTranslation } from "react-i18next";
import Tooltip from "@/Components/General/Tooltip";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import Dropdown from "@/Components/General/Dropdown/Dropdown";
import EllipsisVerticalIcon from "@ui/icons/ellipsis-vertical.svg?react";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import CopyIcon from "@ui/icons/copy.svg?react";
import ArrowRightBracketIcon from "@ui/icons/arrows/arrow-right-bracket.svg?react";

export default function NavbarArkConnect() {
    const { t } = useTranslation();

    const {
        hasExtension,
        isSupported,
        isConnected,
        connect,
        disconnect,
        address,
        addressUrl,
        isOnSameNetwork,
        network,
    } = useArkConnect();

    const copyToClipboard = useCallback(() => {
        window.clipboard(false).copy(address ?? "");
    }, [address]);

    return (
        <div
            className={classNames(
                "flex flex-col px-6 py-3 dark:border-transparent dark:bg-theme-dark-900 dark:text-theme-dark-200 md:px-0 md:py-0",
                {
                    "border-t border-theme-secondary-300 bg-theme-secondary-200 md:border-0 md:bg-transparent":
                        isConnected,
                },
            )}
        >
            {hasExtension && isSupported && !isConnected && (
                <button
                    className="button-secondary whitespace-nowrap px-4 py-1.5"
                    onClick={connect}
                    disabled={!hasExtension}
                >
                    {t("general.navbar.connect_wallet")}
                </button>
            )}

            {/* <x-arkconnect.modal.install-wallet />
    <x-arkconnect.modal.unsupported-browser /> */}

            {isConnected && (
                <div className="flex w-full min-w-0 items-center justify-between space-x-2 md:hidden">
                    <div className="flex min-w-0 items-center space-x-1 text-sm font-semibold">
                        <span className="whitespace-nowrap">{t("general.navbar.arkconnect.my_address")}:</span>

                        {isOnSameNetwork ? (
                            <a href={addressUrl} className="link min-w-0 flex-1">
                                <TruncateMiddle>{address ?? ""}</TruncateMiddle>
                            </a>
                        ) : (
                            <Tooltip content={t(`general.arkconnect.wrong_network.${network!.alias}`)}>
                                <a className="min-w-0 flex-1 text-theme-secondary-500 dark:text-theme-dark-500">
                                    <TruncateMiddle>{address ?? ""}</TruncateMiddle>
                                </a>
                            </Tooltip>
                        )}
                    </div>

                    <div className="flex space-x-4 dark:text-theme-dark-300">
                        <Tooltip content={t("tooltips.copied")}>
                            <button type="button" onClick={copyToClipboard}>
                                <CopyIcon className="h-4 w-4" />
                            </button>
                        </Tooltip>

                        <button type="button" onClick={disconnect} x-on:click="disconnect">
                            <ArrowRightBracketIcon className="h-4 w-4" />
                        </button>
                    </div>
                </div>
            )}
            <div>
                {isConnected && (
                    <div className="hidden md:block">
                        <DropdownProvider>
                            <Dropdown
                                wrapperClass="relative"
                                dropdownClasses="right-0 min-w-[158 px]"
                                useDefaultButtonClasses={false}
                                buttonClass="flex items-center transition-default flex justify-between items-center whitespace-nowrap bg-transparent rounded md:justify-start md:pr-2 md:pl-3 md:space-x-2 md:h-8 md:border border-theme-secondary-300 text-theme-secondary-700 transition-default dark:border-theme-dark-700 dark:text-theme-dark-200 dark:hover:bg-theme-dark-700 hover:text-theme-secondary-900 hover:bg-theme-secondary-200"
                                button={() => (
                                    <>
                                        <div className="text-sm font-semibold md:leading-3.75">
                                            <TruncateMiddle>{address ?? ""}</TruncateMiddle>
                                        </div>

                                        <div className="dark:text-theme-dark-300">
                                            <EllipsisVerticalIcon name="ellipsis-vertical" className="h-4 w-4" />
                                        </div>
                                    </>
                                )}
                            >
                                {isOnSameNetwork && (
                                    <DropdownItem asChild>
                                        <a href={addressUrl}>{t("general.navbar.arkconnect.my_address")}</a>
                                    </DropdownItem>
                                )}

                                {!isOnSameNetwork && (
                                    <DropdownItem disabled>
                                        <Tooltip content={t(`general.arkconnect.wrong_network.${network!.alias}`)}>
                                            <span>{t("general.navbar.arkconnect.my_address")}</span>
                                        </Tooltip>
                                    </DropdownItem>
                                )}

                                <DropdownItem onClick={copyToClipboard}>
                                    {t("general.navbar.arkconnect.copy_address")}
                                </DropdownItem>

                                <DropdownItem onClick={disconnect}>
                                    {t("general.navbar.arkconnect.disconnect")}
                                </DropdownItem>
                            </Dropdown>
                        </DropdownProvider>
                    </div>
                )}
            </div>
        </div>
    );
}
