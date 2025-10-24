import Modal from "@/Components/General/Modal";
import { useTranslation } from "react-i18next";
// arrows.underline-arrow-down
import UnderlineArrowDownIcon from "@ui/icons/arrows/underline-arrow-down.svg?react";
export default function ExportTransactionsModal({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) {
    const { t } = useTranslation();

    return (
        <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
            <Modal.Title>{t("pages.wallet.export-transactions-modal.title")}</Modal.Title>

            <Modal.Body>
                {/* <div class="px-6 pt-4 -mx-6 mt-1 font-normal border-t text-theme-secondary-700 border-theme-secondary-300 dark:text-theme-dark-200 dark:border-theme-dark-700">
                        
                    </div>

                    <div class="px-6 -mx-6 mt-4">
                        <div x-show="! hasStartedExport">
                            <x-modals.export-transactions.fields />
                        </div>

                        <div x-show="hasStartedExport">
                            <x-modals.export.status
                                :partial-download-toast="trans('pages.wallet.export-transactions-modal.success_toast', ['address' => $this->address.'-partial'])"
                            />
                        </div>
                    </div> */}

                <p>{t("pages.wallet.export-transactions-modal.description")}</p>
            </Modal.Body>

            <Modal.Footer>
                <Modal.FooterButtons>
                    <Modal.CancelButton />

                    <Modal.ActionButton className="space-x-2">
                        <UnderlineArrowDownIcon className="h-4 w-4" />
                        <span>{t("actions.export")}</span>
                    </Modal.ActionButton>
                </Modal.FooterButtons>
            </Modal.Footer>
        </Modal>
    );
}
