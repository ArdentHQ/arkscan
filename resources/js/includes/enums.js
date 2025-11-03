export const TransactionType = {
    Transfer: 0,
    SecondSignature: 1,
    DelegateRegistration: 2,
    Vote: 3,
    MultiSignature: 4,
    Ipfs: 5,
    MultiPayment: 6,
    DelegateResignation: 7,
    HtlcLock: 8,
    HtlcClaim: 9,
    HtlcRefund: 10,
    BlsRegistration: 11,
};

export const TransactionTypeGroup = {
    Test: 0,
    Core: 1,
    Magistrate: 2,
};

export const ExportStatus = {
    PendingExport: "PENDING_EXPORT",
    Error: "ERROR",
    Warning: "WARNING",
    PendingDownload: "PENDING_DOWNLOAD",
    Done: "DONE",
};
