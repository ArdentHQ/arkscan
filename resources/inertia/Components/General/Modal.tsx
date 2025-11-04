import { createContext, useContext, forwardRef } from "react";
import * as Dialog from "@radix-ui/react-dialog";
import CrossIcon from "@ui/icons/cross.svg?react";
import { twMerge } from "tailwind-merge";
import { useTranslation } from "react-i18next";

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
const ModalRoot = ({
    isOpen,
    onClose,
    description,
    children,
    ...props
}: React.ComponentProps<typeof Dialog.Root> & {
    isOpen: boolean;
    onClose: () => void;
    description?: React.ReactNode;
}) => (
    <ModalContext.Provider value={{ onClose }}>
        <Dialog.Root open={isOpen} onOpenChange={onClose} {...props}>
            <Dialog.Portal>
                <Dialog.Overlay className="custom-scroll fixed inset-0 z-50 grid place-items-start overflow-y-auto bg-overlay dim:bg-overlay-dim dark:bg-overlay-dark sm:place-items-center md:px-8 md:py-10">
                    <Dialog.Content className="relative w-full max-w-2xl bg-white dark:bg-theme-dark-900 sm:m-auto sm:mx-auto sm:max-w-[448px] sm:rounded-xl sm:shadow-2xl">
                        {description && <Dialog.Description className="sr-only">{description}</Dialog.Description>}
                        {children}
                    </Dialog.Content>
                </Dialog.Overlay>
            </Dialog.Portal>
        </Dialog.Root>
    </ModalContext.Provider>
);

interface ModalTitleProps extends React.HTMLAttributes<HTMLDivElement> {
    hideCloseButton?: boolean;
}

const ModalTitle = ({ children, hideCloseButton = false, className, ...props }: ModalTitleProps) => {
    const { onClose } = useModalContext();

    return (
        <div
            className={twMerge(
                "flex items-center justify-between border-b border-theme-secondary-300 px-6 pb-[0.875rem] pt-4 dark:border-theme-dark-700 sm:pt-[0.875rem]",
                className,
            )}
            {...props}
        >
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
};

const ModalBody = forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
    ({ children, className, ...props }, ref) => (
        <div
            ref={ref}
            className={twMerge(
                "px-6 pb-4 pt-4 font-normal text-theme-secondary-700 dark:text-theme-dark-200 sm:pb-6",
                className,
            )}
            {...props}
        >
            {children}
        </div>
    ),
);

ModalBody.displayName = "ModalBody";

const ModalFooter = ({ children, className, ...props }: React.HTMLAttributes<HTMLDivElement>) => (
    <div
        className={twMerge(
            "mb-4 flex flex-col-reverse border-t border-theme-secondary-300 px-6 pt-4 dark:border-theme-dark-700 sm:flex-row sm:justify-end sm:space-x-3",
            className,
        )}
        {...props}
    >
        {children}
    </div>
);

const ModalFooterButtons = ({ children, className, ...props }: React.HTMLAttributes<HTMLDivElement>) => (
    <div className={twMerge("modal-buttons flex", className)} {...props}>
        {children}
    </div>
);

const ModalCancelButton = ({ children, className, ...props }: React.ButtonHTMLAttributes<HTMLButtonElement>) => {
    const { t } = useTranslation();

    const { onClose } = useModalContext();

    return (
        <button type="button" onClick={onClose} className={twMerge("button button-secondary", className)} {...props}>
            {children ?? t("actions.cancel")}
        </button>
    );
};

const ModalActionButton = ({ className, ...props }: React.ButtonHTMLAttributes<HTMLButtonElement>) => {
    return (
        <button
            type="button"
            className={twMerge(
                "button button-primary flex items-center justify-center sm:mb-0 sm:px-4 sm:py-1.5",
                className,
            )}
            {...props}
        />
    );
};

const Modal = Object.assign(ModalRoot, {
    Title: ModalTitle,
    Body: ModalBody,
    Footer: ModalFooter,
    FooterButtons: ModalFooterButtons,
    CancelButton: ModalCancelButton,
    ActionButton: ModalActionButton,
});

export default Modal;
