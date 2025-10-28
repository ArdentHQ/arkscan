import { ExportStatus as ExportStatusEnum } from "@js/includes/enums";
import { useTranslation } from "react-i18next";
import CsvIcon from "@icons/csv.svg?react";
import CheckMarkSmallIcon from "@ui/icons/check-mark-small.svg?react";
import CrossSmallIcon from "@ui/icons/cross-small.svg?react";
import LoadingImage from "@images/modals/export/loading.svg?react";
import LoadingDarkImage from "@images/modals/export/loading-dark.svg?react";
import SuccessImage from "@images/modals/export/success.svg?react";
import SuccessDarkImage from "@images/modals/export/success-dark.svg?react";
import ErrorImage from "@images/modals/export/error.svg?react";
import ErrorDarkImage from "@images/modals/export/error-dark.svg?react";
import WarningImage from "@images/modals/export/warning.svg?react";
import WarningDarkImage from "@images/modals/export/warning-dark.svg?react";
import Alert from "@/Components/General/Alert";

interface ExportStatusProps {
    status: string;
    dataUri: string | null;
    partialDataUri: string | null;
    errorMessage: string | null;
    successMessage: string | null;
    address: string;
}

export default function ExportStatus({
    status,
    dataUri,
    partialDataUri,
    errorMessage,
    successMessage,
    address = "",
}: ExportStatusProps) {
    const { t } = useTranslation();

    const handleDownload = (uri: string, isPartial = false) => {
        const link = document.createElement("a");
        link.href = uri;
        link.download = `${(address || "unknown").substring(0, 5)}...${(address || "unknown").substring((address || "unknown").length - 5)}${isPartial ? "-partial" : ""}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const downloadUri = dataUri || partialDataUri;
    const canDownload =
        (status === ExportStatusEnum.Done && dataUri) || (status === ExportStatusEnum.Warning && partialDataUri);

    const addressDisplay =
        address && address.length >= 10
            ? `${address.substring(0, 5)}...${address.substring(address.length - 5)}.csv`
            : "export.csv";

    return (
        <div className="flex flex-col">
            <div className="mb-6 flex justify-center">
                {status === ExportStatusEnum.PendingDownload && (
                    <>
                        <LoadingImage className="dark:hidden" />
                        <LoadingDarkImage className="hidden dark:block" />
                    </>
                )}

                {status === ExportStatusEnum.Error && (
                    <>
                        <ErrorImage className="dark:hidden" />
                        <ErrorDarkImage className="hidden dark:block" />
                    </>
                )}

                {status === ExportStatusEnum.Warning && (
                    <>
                        <WarningImage className="dark:hidden" />
                        <WarningDarkImage className="hidden dark:block" />
                    </>
                )}

                {status === ExportStatusEnum.Done && (
                    <>
                        <SuccessImage className="dark:hidden" />
                        <SuccessDarkImage className="hidden dark:block" />
                    </>
                )}
            </div>

            <div className="mb-4">
                {status === ExportStatusEnum.PendingDownload && (
                    <Alert
                        title={t("general.information")}
                        message={t("general.export.information_text")}
                        type="info"
                    />
                )}

                {status === ExportStatusEnum.Done && (
                    <Alert title={t("general.success")} type="success">
                        {successMessage}
                    </Alert>
                )}

                {status === ExportStatusEnum.Error && (
                    <Alert title={t("general.error")} type="error">
                        <div className="flex flex-col space-y-2">
                            <div>{errorMessage}</div>

                            {partialDataUri && (
                                <div className="flex space-x-1">
                                    <span>{t("general.export.partial.click")}</span>

                                    <button
                                        type="button"
                                        onClick={() => handleDownload(partialDataUri, true)}
                                        className="link inline"
                                    >
                                        {t("general.export.partial.here")}
                                    </button>

                                    <span>{t("general.export.partial.to_download")}</span>
                                </div>
                            )}
                        </div>
                    </Alert>
                )}

                {status === ExportStatusEnum.Warning && (
                    <Alert
                        title={t("general.warning")}
                        message={t("general.export.warning_text", { type: "transactions" })}
                        type="warning"
                    />
                )}
            </div>

            <button
                type="button"
                onClick={
                    canDownload && downloadUri
                        ? () => handleDownload(downloadUri, status === ExportStatusEnum.Warning)
                        : undefined
                }
                disabled={!canDownload}
                className="flex items-center justify-between rounded border border-theme-secondary-300 px-4 disabled:cursor-default dark:border-theme-dark-500"
            >
                <div className="flex h-12 min-w-0 items-center space-x-2 font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                    <CsvIcon className="fill-theme-primary-600 dark:fill-theme-dark-blue-500" />

                    <div className="truncate">{addressDisplay}</div>
                </div>

                <div className="relative">
                    {status === ExportStatusEnum.PendingDownload && (
                        <div className="h-8 w-8">
                            <svg
                                className="h-8 w-8 animate-spin"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <circle
                                    className="stroke-theme-primary-100 dark:stroke-theme-dark-700"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    strokeWidth="4"
                                    fill="none"
                                />
                                <path
                                    className="fill-theme-primary-600 dark:fill-theme-dark-blue-400"
                                    d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"
                                />
                            </svg>
                        </div>
                    )}

                    {status !== ExportStatusEnum.PendingDownload && (
                        <div
                            className={`flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full ${
                                [ExportStatusEnum.Done, ExportStatusEnum.Warning].includes(status)
                                    ? "bg-theme-primary-100 dark:bg-theme-dark-blue-500"
                                    : status === ExportStatusEnum.Error
                                      ? "bg-theme-danger-100 dark:bg-theme-danger-400"
                                      : ""
                            }`}
                        >
                            {[ExportStatusEnum.Done, ExportStatusEnum.Warning].includes(status) && (
                                <CheckMarkSmallIcon className="h-2.5 w-2.5 text-theme-primary-600 dark:text-theme-dark-50" />
                            )}

                            {status === ExportStatusEnum.Error && (
                                <CrossSmallIcon className="h-2.5 w-2.5 text-theme-danger-400 dark:text-theme-dark-50" />
                            )}
                        </div>
                    )}
                </div>
            </button>
        </div>
    );
}
