import { twMerge } from "tailwind-merge";
import { useState } from "react";
import CircleInfoIcon from "@ui/icons/circle/info.svg?react";
import CircleCheckMarkIcon from "@ui/icons/circle/check-mark.svg?react";
import CircleExclamationMarkIcon from "@ui/icons/circle/exclamation-mark.svg?react";
import CrossIcon from "@ui/icons/cross.svg?react";

type AlertType = "info" | "success" | "error" | "warning";

interface AlertProps extends React.HTMLAttributes<HTMLDivElement> {
    title: string;
    message?: string;
    type?: AlertType;
    dismissible?: boolean;
    children?: React.ReactNode;
}

const alertClasses: Record<AlertType, string> = {
    info: "alert-info",
    success: "alert-success",
    error: "alert-error",
    warning: "alert-warning",
};

const alertIcons: Record<AlertType, React.ReactNode> = {
    info: <CircleInfoIcon className="alert-icon" />,
    success: <CircleCheckMarkIcon className="alert-icon" />,
    error: <CircleExclamationMarkIcon className="alert-icon" />,
    warning: <CircleExclamationMarkIcon className="alert-icon" />,
};

export default function Alert({
    title,
    message,
    type = "info",
    dismissible = false,
    children,
    className,
    ...props
}: AlertProps) {
    const [show, setShow] = useState(true);

    if (!show) return null;

    return (
        <div className={twMerge("alert-wrapper", alertClasses[type], className)} {...props}>
            <div className="alert-content-wrapper">
                <h2 className="alert-title">
                    {alertIcons[type]}
                    <span>{title}</span>
                    {dismissible && (
                        <button
                            type="button"
                            onClick={() => setShow(false)}
                            aria-label="Dismiss"
                            className="alert-dismiss"
                        >
                            <CrossIcon className="h-3 w-3" />
                        </button>
                    )}
                </h2>
                <span className="alert-content">{message || children}</span>
            </div>
        </div>
    );
}
