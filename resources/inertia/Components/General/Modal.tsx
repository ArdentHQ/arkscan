import { useEffect, useRef } from "react";
import { createFocusTrap } from "focus-trap";
import { disableBodyScroll, enableBodyScroll } from "body-scroll-lock";
import classNames from "@/utils/class-names";

interface ModalProps extends React.PropsWithChildren {
    isOpen: boolean;
    onClose: () => void;
    title?: React.ReactNode;
    footer?: React.ReactNode;
    className?: string;
    closeOnEscape?: boolean;
    closeOnBackdrop?: boolean;
}

export default function Modal({
    isOpen,
    onClose,
    title,
    footer,
    children,
    className = "",
    closeOnEscape = true,
    closeOnBackdrop = false,
}: ModalProps) {
    const modalRef = useRef<HTMLDivElement>(null);
    const focusTrapRef = useRef<ReturnType<typeof createFocusTrap> | null>(null);

    useEffect(() => {
        if (!isOpen || !modalRef.current) return;

        // Trap focus inside modal
        focusTrapRef.current = createFocusTrap(modalRef.current, {
            onDeactivate: onClose,
        });
        focusTrapRef.current.activate();

        // Prevent scroll on body
        disableBodyScroll(modalRef.current);

        // Handle ESC key
        const handleEscape = (e: KeyboardEvent) => {
            if (closeOnEscape && e.key === "Escape") {
                onClose();
            }
        };

        document.addEventListener("keydown", handleEscape);

        return () => {
            focusTrapRef.current?.deactivate();
            enableBodyScroll(modalRef.current!);
            document.removeEventListener("keydown", handleEscape);
        };
    }, [isOpen, closeOnEscape, onClose]);

    if (!isOpen) return null;

    const handleBackdropClick = () => {
        if (closeOnBackdrop) {
            onClose();
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <div
                className="bg-theme-secondary-900/75 dark:bg-theme-secondary-800/50 fixed inset-0"
                onClick={handleBackdropClick}
                aria-hidden="true"
            />

            {/* Modal */}
            <div
                ref={modalRef}
                className={classNames({
                    "relative z-50 w-full rounded-xl bg-white shadow-2xl dark:bg-theme-dark-900": true,
                    "sm:max-w-lg": true,
                    [className]: true,
                })}
                role="dialog"
                aria-modal="true"
            >
                {/* Header */}
                {title && (
                    <div className="border-b border-theme-secondary-300 px-6 py-4 dark:border-theme-dark-700">
                        <h2 className="text-lg font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                            {title}
                        </h2>
                    </div>
                )}

                {/* Content */}
                <div className="px-6 py-4">{children}</div>

                {/* Footer */}
                {footer && (
                    <div className="border-t border-theme-secondary-300 px-6 py-4 dark:border-theme-dark-700">
                        {footer}
                    </div>
                )}
            </div>
        </div>
    );
}
