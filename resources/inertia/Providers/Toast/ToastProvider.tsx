import { useCallback, useEffect, useMemo, useState } from "react";
import { useTranslation } from "react-i18next";
import CrossIcon from "@ui/icons/cross.svg?react";
import CircleInfoIcon from "@ui/icons/circle/info.svg?react";
import CircleCheckMarkIcon from "@ui/icons/circle/check-mark.svg?react";
import CircleExclamationMarkIcon from "@ui/icons/circle/exclamation-mark.svg?react";
import CircleCrossIcon from "@ui/icons/circle/cross.svg?react";
import CircleQuestionMarkIcon from "@ui/icons/circle/question-mark.svg?react";
import ToastContext from "./ToastContext";
import { ToastContextValue, ToastEventDetail, ToastMessage, ToastOptions, ToastType } from "./types";

const toastClassMap: Record<ToastType, string> = {
    info: "toast-info",
    success: "toast-success",
    warning: "toast-warning",
    error: "toast-danger",
    danger: "toast-danger",
    hint: "toast-hint",
};

const toastIconMap: Record<ToastType, React.FC<React.SVGProps<SVGSVGElement>>> = {
    info: CircleInfoIcon,
    success: CircleCheckMarkIcon,
    warning: CircleExclamationMarkIcon,
    error: CircleCrossIcon,
    danger: CircleCrossIcon,
    hint: CircleQuestionMarkIcon,
};

const defaultDuration = 5000;

const ToastItem = ({ toast, onDismiss }: { toast: ToastMessage; onDismiss: (id: string) => void }) => {
    const { t } = useTranslation();

    useEffect(() => {
        if (toast.duration <= 0) {
            return;
        }

        const timer = window.setTimeout(() => onDismiss(toast.id), toast.duration);

        return () => window.clearTimeout(timer);
    }, [toast.duration, toast.id, onDismiss]);

    const Icon = toastIconMap[toast.type] ?? CircleInfoIcon;
    const toastClass = toastClassMap[toast.type] ?? toastClassMap.info;

    return (
        <div className={`toast ${toastClass}`} role="alert" aria-live="polite">
            <span className="toast-icon">
                <Icon className="h-4 w-4" />

                <span className="text-sm font-semibold sm:hidden">{t(`toasts.${toast.type}`, { ns: "ui" })}</span>
            </span>

            <div className="toast-body mr-4">
                {toast.html ? (
                    <span dangerouslySetInnerHTML={{ __html: toast.message }} />
                ) : (
                    <span>{toast.message}</span>
                )}
            </div>

            <button
                type="button"
                className="toast-button"
                aria-label={t("actions.close")}
                onClick={(event) => {
                    event.stopPropagation();
                    onDismiss(toast.id);
                }}
            >
                <CrossIcon className="h-3 w-3" />
            </button>
        </div>
    );
};

const ToastContainer = ({ toasts, onDismiss }: { toasts: ToastMessage[]; onDismiss: (id: string) => void }) => {
    if (toasts.length === 0) {
        return null;
    }

    return (
        <div className="fixed bottom-0 right-0 z-50 flex flex-col items-end space-y-3 p-5">
            {toasts.map((toast) => (
                <div key={toast.id} className="z-20 flex cursor-pointer" onClick={() => onDismiss(toast.id)}>
                    <ToastItem toast={toast} onDismiss={onDismiss} />
                </div>
            ))}
        </div>
    );
};

export default function ToastProvider({ children }: { children: React.ReactNode }) {
    const [toasts, setToasts] = useState<ToastMessage[]>([]);

    const dismissToast = useCallback((id: string) => {
        setToasts((current) => current.filter((toast) => toast.id !== id));
    }, []);

    const addToast = useCallback((message: string, options?: ToastOptions) => {
        const id =
            typeof crypto !== "undefined" && "randomUUID" in crypto
                ? crypto.randomUUID()
                : `${Date.now()}-${Math.random().toString(16).slice(2)}`;

        const toast: ToastMessage = {
            id,
            message,
            type: options?.type ?? "info",
            duration: options?.duration ?? defaultDuration,
            html: options?.html ?? false,
        };

        setToasts((current) => [...current, toast]);

        return id;
    }, []);

    useEffect(() => {
        if (typeof window === "undefined") {
            return undefined;
        }

        const handler = (event: Event) => {
            const detail = (event as CustomEvent<ToastEventDetail>).detail;
            if (!detail?.message) {
                return;
            }

            const { message, ...options } = detail;
            addToast(message, options);
        };

        window.addEventListener("toastMessage", handler as EventListener);

        return () => window.removeEventListener("toastMessage", handler as EventListener);
    }, [addToast]);

    const value = useMemo<ToastContextValue>(
        () => ({
            addToast,
            dismissToast,
        }),
        [addToast, dismissToast],
    );

    return (
        <ToastContext.Provider value={value}>
            {children}
            <ToastContainer toasts={toasts} onDismiss={dismissToast} />
        </ToastContext.Provider>
    );
}
