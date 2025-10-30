import Modal from "@/Components/General/Modal";
import Select from "@/Components/General/Select";
import DatePicker from "@/Components/General/DatePicker";
import Checkbox from "@/Components/General/Checkbox";
import TransactionTypesSelect from "./ExportTransactionsModal/TransactionTypesSelect";
import TransactionColumnsSelect from "./ExportTransactionsModal/TransactionColumnsSelect";
import ExportStatus from "./ExportTransactionsModal/ExportStatus";
import useExportTransactions from "./ExportTransactionsModal/useExportTransactions";
import { useEffect, useRef } from "react";
import { useTranslation } from "react-i18next";
import UnderlineArrowDownIcon from "@ui/icons/arrows/underline-arrow-down.svg?react";
import Label from "@/Components/General/Label";
// @ts-ignore
import { ExportStatus as ExportStatusEnum } from "@js/includes/enums";

interface ExportTransactionsModalProps {
    isOpen: boolean;
    onClose: () => void;
    address: string;
    network: any;
    userCurrency: string;
    rates: Record<string, number>;
    canBeExchanged: boolean;
}

export default function ExportTransactionsModal({
    isOpen,
    onClose,
    address,
    network,
    userCurrency,
    rates,
    canBeExchanged,
}: ExportTransactionsModalProps) {
    const { t } = useTranslation();

    const {
        // Form state
        dateRange,
        setDateRange,
        dateFrom,
        setDateFrom,
        dateTo,
        setDateTo,
        delimiter,
        setDelimiter,
        includeHeaderRow,
        setIncludeHeaderRow,
        selectedTypes,
        setSelectedTypes,
        selectedColumns,
        setSelectedColumns,
        // Export state
        hasStartedExport,
        setHasStartedExport,
        dataUri,
        partialDataUri,
        exportStatus,
        errorMessage,
        successMessage,
        // Methods
        canExport,
        exportData,
    } = useExportTransactions({
        isOpen,
        address,
        network,
        userCurrency,
        rates,
        canBeExchanged,
    });

    return (
        <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
            <Modal.Title>{t("pages.wallet.export-transactions-modal.title")}</Modal.Title>

            <Modal.Body>
                <p className="mb-4">{t("pages.wallet.export-transactions-modal.description")}</p>

                {!hasStartedExport ? (
                    <>
                        <div className="space-y-5">
                            <div>
                                <Label>{t("pages.wallet.export-transactions-modal.date_range")}</Label>

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
                            </div>

                            {dateRange === "custom" && (
                                <div className="-mx-6 mt-4 flex space-x-3 bg-theme-primary-50 px-6 py-4 dark:bg-theme-dark-950">
                                    <div className="flex flex-1 flex-col space-y-2">
                                        <label className="dark:text-theme-dark-200">
                                            {t("general.export.date_from")}
                                        </label>

                                        <DatePicker
                                            value={dateFrom}
                                            onChange={(date) => {
                                                setDateFrom(date);
                                            }}
                                            maxDate={dateTo || undefined}
                                        />
                                    </div>

                                    <div className="flex flex-1 flex-col space-y-2">
                                        <label className="dark:text-theme-dark-200">
                                            {t("general.export.date_to")}
                                        </label>

                                        <DatePicker
                                            value={dateTo}
                                            onChange={(date) => {
                                                setDateTo(date);
                                            }}
                                            minDate={dateFrom || undefined}
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="flex flex-col space-y-3">
                                <div>
                                    <Label>{t("pages.wallet.export-transactions-modal.delimiter")}</Label>

                                    <Select value={delimiter} onValueChange={setDelimiter}>
                                        <Select.Trigger />

                                        <Select.Content className="w-full sm:w-100">
                                            <Select.Item value="comma">
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.comma.text",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ({" "}
                                                </span>
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.comma.value",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ){" "}
                                                </span>
                                            </Select.Item>
                                            <Select.Item value="semicolon">
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.semicolon.text",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ({" "}
                                                </span>
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.semicolon.value",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ){" "}
                                                </span>
                                            </Select.Item>
                                            <Select.Item value="tab">
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.tab.text",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ({" "}
                                                </span>
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.tab.value",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ){" "}
                                                </span>
                                            </Select.Item>
                                            <Select.Item value="pipe">
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.pipe.text",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ({" "}
                                                </span>
                                                <span>
                                                    {t(
                                                        "pages.wallet.export-transactions-modal.delimiter-options.pipe.value",
                                                    )}
                                                </span>
                                                <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                                    {" "}
                                                    ){" "}
                                                </span>
                                            </Select.Item>
                                        </Select.Content>
                                    </Select>
                                </div>

                                <Checkbox>
                                    <Checkbox.Input
                                        checked={includeHeaderRow}
                                        onCheckedChange={setIncludeHeaderRow}
                                        className="checked:border-theme-primary-600 checked:bg-theme-primary-600 hover:checked:bg-theme-primary-700 dim:checked:border-theme-dark-blue-500 dim:checked:bg-theme-dark-blue-500 dim:hover:checked:bg-theme-dark-blue-600 dark:checked:border-theme-dark-blue-500 dark:checked:bg-theme-dark-blue-500 dark:hover:checked:bg-theme-dark-blue-600"
                                    />

                                    <Checkbox.Label>
                                        {t("pages.wallet.export-transactions-modal.include_header_row")}
                                    </Checkbox.Label>
                                </Checkbox>
                            </div>

                            <div>
                                <Label>{t("pages.wallet.export-transactions-modal.types")}</Label>

                                <TransactionTypesSelect value={selectedTypes} onValueChange={setSelectedTypes} />
                            </div>

                            <div>
                                <Label>{t("pages.wallet.export-transactions-modal.columns")}</Label>

                                <TransactionColumnsSelect value={selectedColumns} onValueChange={setSelectedColumns} />
                            </div>
                        </div>
                    </>
                ) : (
                    <ExportStatus
                        status={exportStatus || ""}
                        dataUri={dataUri}
                        partialDataUri={partialDataUri}
                        errorMessage={errorMessage}
                        successMessage={successMessage}
                        address={address}
                    />
                )}
            </Modal.Body>

            <Modal.Footer>
                <Modal.FooterButtons>
                    {!hasStartedExport ? (
                        <>
                            <button type="button" className="button-secondary" onClick={onClose}>
                                {t("actions.cancel")}
                            </button>

                            <Modal.ActionButton className="space-x-2" disabled={!canExport()} onClick={exportData}>
                                <UnderlineArrowDownIcon className="h-4 w-4" />
                                <span>{t("actions.export")}</span>
                            </Modal.ActionButton>
                        </>
                    ) : exportStatus === ExportStatusEnum.Error ? (
                        <>
                            <button
                                type="button"
                                className="button-secondary"
                                onClick={() => setHasStartedExport(false)}
                            >
                                {t("actions.back")}
                            </button>

                            <button
                                type="button"
                                className="button-primary flex items-center justify-center space-x-2"
                                onClick={exportData}
                            >
                                <UnderlineArrowDownIcon className="h-4 w-4" />
                                <span>{t("actions.retry")}</span>
                            </button>
                        </>
                    ) : (
                        <>
                            {dataUri === null && (
                                <button
                                    type="button"
                                    className="button-secondary"
                                    onClick={() => setHasStartedExport(false)}
                                >
                                    {t("actions.back")}
                                </button>
                            )}

                            {dataUri !== null && (
                                <button type="button" className="button-secondary" onClick={onClose}>
                                    {t("actions.close")}
                                </button>
                            )}

                            <a
                                href={dataUri || ""}
                                className={dataUri ? "button-primary" : "button-primary pointer-events-none opacity-50"}
                                download={`${address}.csv`}
                            >
                                <span className="flex items-center space-x-2">
                                    <UnderlineArrowDownIcon className="h-4 w-4" />
                                    <span>{t("actions.download")}</span>
                                </span>
                            </a>
                        </>
                    )}
                </Modal.FooterButtons>
            </Modal.Footer>
        </Modal>
    );
}
