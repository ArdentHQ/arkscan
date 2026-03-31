export const ExportStatus = {
    PendingExport: "PENDING_EXPORT",
    Error: "ERROR",
    Warning: "WARNING",
    PendingDownload: "PENDING_DOWNLOAD",
    Done: "DONE",
} as const;

export type ExportStatusType = (typeof ExportStatus)[keyof typeof ExportStatus];
