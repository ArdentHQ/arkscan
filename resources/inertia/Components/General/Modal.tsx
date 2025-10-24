import { createContext, useContext, ReactNode } from "react";
import * as Dialog from "@radix-ui/react-dialog";
import CrossIcon from "@ui/icons/cross.svg?react";
import classNames from "@/utils/class-names";
import { twMerge } from "tailwind-merge";

// Context for sharing modal state
interface ModalContextType {
    onClose: () => void;
}

const ModalContext = createContext<ModalContextType | undefined>(undefined);

const useModalContext = () => {
    const context = useContext(ModalContext);
    if (!context) {
        throw new Error("Modal subcomponents must be used within <Modal>");
    }
    return context;
};

// Main Modal Component
interface ModalProps extends React.PropsWithChildren {
    isOpen: boolean;
    onClose: () => void;
    description?: React.ReactNode;
}

function ModalRoot({ isOpen, onClose, description, children }: ModalProps) {
    return (
        <ModalContext.Provider value={{ onClose }}>
            <Dialog.Root open={isOpen} onOpenChange={onClose}>
                <Dialog.Portal>
                    <Dialog.Overlay className="dark:bg-theme-secondary-800/50 fixed inset-0 z-50 bg-theme-secondary-900 opacity-75 dim:bg-theme-dark-950 dark:opacity-50" />

                    <div className="fixed inset-0 z-50 grid place-items-start overflow-y-auto sm:place-items-center md:px-8 md:py-10">
                        <Dialog.Content className="custom-scroll relative w-full max-w-2xl bg-white dark:bg-theme-dark-900 sm:m-auto sm:mx-auto sm:max-w-[448px] sm:rounded-xl sm:shadow-2xl">
                            {description && <Dialog.Description className="sr-only">{description}</Dialog.Description>}
                            {children}
                        </Dialog.Content>
                    </div>
                </Dialog.Portal>
            </Dialog.Root>
        </ModalContext.Provider>
    );
}

// Modal.Title
interface ModalTitleProps {
    children: ReactNode;
    hideCloseButton?: boolean;
}

function ModalTitle({ children, hideCloseButton = false }: ModalTitleProps) {
    const { onClose } = useModalContext();

    return (
        <div className="flex items-center justify-between border-b border-theme-secondary-300 px-6 pb-[0.875rem] pt-4 dark:border-theme-dark-700 sm:pb-4 sm:pt-[0.875rem]">
            <Dialog.Title className="m-0 text-left text-lg font-semibold dark:text-theme-dark-50">
                {children}
            </Dialog.Title>

            {!hideCloseButton && (
                <button
                    type="button"
                    onClick={onClose}
                    className="button button-secondary m-0 h-6 w-6 rounded-none bg-transparent p-0 text-theme-secondary-700 dim:bg-transparent dim:shadow-none dark:bg-transparent dark:text-theme-dark-200 dark:shadow-none hover:dark:bg-theme-dark-blue-600 hover:dark:text-theme-dark-50 sm:rounded"
                >
                    <CrossIcon className="fill-current m-auto h-4 w-4" />
                </button>
            )}
        </div>
    );
}

// Modal.Body
interface ModalBodyProps extends React.PropsWithChildren {
    className?: string;
}

function ModalBody({ children, className = "" }: ModalBodyProps) {
    return (
        <div
            className={classNames({
                "px-6 pb-4 pt-4 font-normal text-theme-secondary-700 dark:text-theme-dark-200 sm:pb-6": true,
                [className]: true,
            })}
        >
            {children}
        </div>
    );
}

// Modal.Footer
interface ModalFooterProps extends React.PropsWithChildren {
    className?: string;
}

function ModalFooter({ children, className = "" }: ModalFooterProps) {
    return (
        <div
            className={classNames({
                "mb-4 flex flex-col-reverse border-t border-theme-secondary-300 px-6 pt-4 dark:border-theme-dark-700 sm:flex-row sm:justify-end sm:space-x-3": true,
                [className]: true,
            })}
        >
            {children}
        </div>
    );
}

// Modal.CancelButton
interface ModalCancelButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    children?: ReactNode;
    asChild?: boolean;
}

function ModalCancelButton({ children = "Cancel", asChild = false, ...props }: ModalCancelButtonProps) {
    const { onClose } = useModalContext();

    if (asChild) {
        return <>{children}</>;
    }

    return (
        <button type="button" onClick={onClose} className="button button-secondary" {...props}>
            {children}
        </button>
    );
}

// Modal.ActionButton
interface ModalActionButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    children?: ReactNode;
    asChild?: boolean;
}

function ModalActionButton({ children = "Action", asChild = false, ...props }: ModalActionButtonProps) {
    if (asChild) {
        return <>{children}</>;
    }

    return (
        <button
            type="button"
            className="button button-primary flex items-center justify-center sm:mb-0 sm:px-4 sm:py-1.5"
            {...props}
        >
            {children}
        </button>
    );
}

const ModalFooterButtons = ({ children, className, ...props }: React.HTMLAttributes<HTMLDivElement>) => {
    return <div className={twMerge("modal-buttons flex", className)} {...props} />;
};

// Compound component export
const Modal = Object.assign(ModalRoot, {
    Title: ModalTitle,
    Body: ModalBody,
    Footer: ModalFooter,
    CancelButton: ModalCancelButton,
    ActionButton: ModalActionButton,
    FooterButtons: ModalFooterButtons,
});

export default Modal;
