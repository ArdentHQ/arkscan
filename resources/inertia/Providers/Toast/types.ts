export type ToastType = "info" | "success" | "warning" | "error" | "danger" | "hint";

export type ToastOptions = {
    type?: ToastType;
    duration?: number;
    html?: boolean;
};

export type ToastMessage = {
    id: string;
    message: string;
    type: ToastType;
    duration: number;
    html: boolean;
};

export type ToastContextValue = {
    addToast: (message: string, options?: ToastOptions) => string;
    dismissToast: (id: string) => void;
};

export type ToastEventDetail = ToastOptions & {
    message: string;
};
