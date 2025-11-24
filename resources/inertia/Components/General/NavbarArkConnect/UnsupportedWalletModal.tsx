import { useState } from "react";
import Modal from "@/Components/General/Modal";
import { useTranslation } from "react-i18next";
import UnsupportedIcon from "@images/modals/arkconnect/unsupported.svg?react";
import UnsupportedDarkIcon from "@images/modals/arkconnect/unsupported-dark.svg?react";
import UnsupportedDimIcon from "@images/modals/arkconnect/unsupported-dim.svg?react";
import Alert from "@/Components/General/Alert";

export default function UnsupportedWalletModal() {
    const [isOpen, setIsOpen] = useState(false);

    const onClose = () => {
        setIsOpen(false);
    };

    const { t } = useTranslation();

    return (
        <>
            <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
                <Modal.Title className="mt-3 pb-5">
                    {t("general.navbar.arkconnect.modal.unsupported_browser_title")}
                </Modal.Title>

                <Modal.Body>
                    <div className="flex-col items-center space-y-4 sm:space-y-6">
                        <div className="mx-auto flex h-[104px] items-center justify-center">
                            <UnsupportedIcon className="dark:hidden" />
                            <UnsupportedDarkIcon className="hidden dim:hidden dark:block" />
                            <UnsupportedDimIcon className="hidden dim:block" />
                        </div>

                        <Alert type="warning">{t("general.navbar.arkconnect.modal.unsupported_browser_warning")}</Alert>
                    </div>
                </Modal.Body>

                <Modal.Footer>
                    <Modal.FooterButtons>
                        <button type="button" className="button-secondary" onClick={onClose}>
                            {t("actions.cancel")}
                        </button>

                        <Modal.ActionButton asChild>
                            <a target="_blank" rel="noopener nofollow noreferrer" href="https://arkconnect.io">
                                {t("actions.learn_more")}
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
