import { useState } from "react";
import Modal from "@/Components/General/Modal";
import { useTranslation } from "react-i18next";
import InstallIcon from "@images/modals/arkconnect/install.svg?react";
import InstallDarkIcon from "@images/modals/arkconnect/install-dark.svg?react";
import InstallDimIcon from "@images/modals/arkconnect/install-dim.svg?react";
import ArkConnectIcon from "@icons/wallets/arkconnect.svg?react";

export default function InstallWalletModal() {
    const [isOpen, setIsOpen] = useState(false);

    const onClose = () => {
        setIsOpen(false);
    };

    const { t } = useTranslation();

    return (
        <>
            <Modal isOpen={isOpen} onClose={onClose}>
                <Modal.Title className="mt-3 pb-5">
                    {t("general.navbar.arkconnect.modal.install_title")}

                    <div className="mt-1.5 text-sm font-normal leading-5.25 text-theme-secondary-700 dark:text-theme-dark-200">
                        {t("general.navbar.arkconnect.modal.install_subtitle")}
                    </div>
                </Modal.Title>

                <Modal.Body>
                    <div className="mx-auto w-[301px]">
                        <InstallIcon className="dark:hidden" />
                        <InstallDarkIcon className="hidden dim:hidden dark:block" />
                        <InstallDimIcon className="hidden dim:block" />
                    </div>
                </Modal.Body>

                <Modal.Footer>
                    <Modal.FooterButtons>
                        <button type="button" className="button-secondary" onClick={onClose}>
                            {t("actions.cancel")}
                        </button>

                        <Modal.ActionButton asChild>
                            <a target="_blank" rel="noopener nofollow noreferrer" href="https://arkconnect.io">
                                <div className="flex h-full items-center justify-center space-x-2">
                                    <ArkConnectIcon className="h-4 w-4" />
                                    <span>{t("general.navbar.arkconnect.modal.install_arkconnect")}</span>
                                </div>
                            </a>
                        </Modal.ActionButton>
                    </Modal.FooterButtons>
                </Modal.Footer>
            </Modal>

            <button
                type="button"
                className="button-secondary w-full whitespace-nowrap px-4 py-1.5 md:w-auto"
                onClick={() => setIsOpen(true)}
            >
                {t("general.navbar.connect_wallet")}
            </button>
        </>
    );
}
