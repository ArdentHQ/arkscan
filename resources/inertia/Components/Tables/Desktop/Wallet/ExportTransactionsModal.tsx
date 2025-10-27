import Modal from "@/Components/General/Modal";
import Select from "@/Components/General/Select";
import DatePicker from "@/Components/General/DatePicker";
import { useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import UnderlineArrowDownIcon from "@ui/icons/arrows/underline-arrow-down.svg?react";

export default function ExportTransactionsModal({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) {
    const { t } = useTranslation();
    const [dateRange, setDateRange] = useState<string>("current_month");
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);
    const modalBodyRef = useRef<HTMLDivElement>(null);

    return (
        <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
            <Modal.Title>{t("pages.wallet.export-transactions-modal.title")}</Modal.Title>

            <Modal.Body ref={modalBodyRef}>
                <p className="mb-4">{t("pages.wallet.export-transactions-modal.description")}</p>

                <div className="space-y-4">
                    <Select value={dateRange} onValueChange={setDateRange}>
                        <Select.Trigger />

                        <Select.Content className="w-full sm:w-100">
                            <Select.Item value="current_month">
                                {t("pages.wallet.export-transactions-modal.date-options.current_month")}
                            </Select.Item>
                            <Select.Item value="last_month">
                                {t("pages.wallet.export-transactions-modal.date-options.last_month")}
                            </Select.Item>
                            <Select.Item value="last_quarter">
                                {t("pages.wallet.export-transactions-modal.date-options.last_quarter")}
                            </Select.Item>
                            <Select.Item value="current_year">
                                {t("pages.wallet.export-transactions-modal.date-options.current_year")}
                            </Select.Item>
                            <Select.Item value="last_year">
                                {t("pages.wallet.export-transactions-modal.date-options.last_year")}
                            </Select.Item>
                            <Select.Item value="all">
                                {t("pages.wallet.export-transactions-modal.date-options.all")}
                            </Select.Item>
                            <Select.Separator />
                            <Select.Item value="custom">{t("general.custom")}</Select.Item>
                        </Select.Content>
                    </Select>

                    {dateRange === "custom" && (
                        <div className="-mx-6 mt-4 flex space-x-3 bg-theme-primary-50 px-6 py-4 dark:bg-theme-dark-950">
                            <div className="flex flex-1 flex-col space-y-2">
                                <label className="dark:text-theme-dark-200">{t("general.export.date_from")}</label>

                                <DatePicker
                                    container={modalBodyRef.current}
                                    value={dateFrom}
                                    onChange={(date) => {
                                        setDateFrom(date);
                                    }}
                                    maxDate={dateTo || undefined}
                                />
                            </div>

                            <div className="flex flex-1 flex-col space-y-2">
                                <label className="dark:text-theme-dark-200">{t("general.export.date_to")}</label>

                                <DatePicker
                                    container={modalBodyRef.current}
                                    value={dateTo}
                                    onChange={(date) => {
                                        setDateTo(date);
                                    }}
                                    minDate={dateFrom || undefined}
                                />
                            </div>
                        </div>
                    )}

                    {/* <div class="px-6 -mx-6 mt-4"> 
                        <div x-show="! hasStartedExport">
                            <x-modals.export-transactions.fields />
                        </div>

                        <div x-show="hasStartedExport">
                            <x-modals.export.status
                                :partial-download-toast="trans('pages.wallet.export-transactions-modal.success_toast', ['address' => $this->address.'-partial'])"
                            />
                        </div>
                    </div> */}
                </div>
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
