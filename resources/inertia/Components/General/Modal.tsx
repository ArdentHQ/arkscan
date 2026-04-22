import { createContext, useContext, forwardRef } from "react";
import * as Dialog from "@radix-ui/react-dialog";
import CrossIcon from "@ui/icons/cross.svg?react";
import { twMerge } from "tailwind-merge";
import { useTranslation } from "react-i18next";
import { Slot } from "@radix-ui/react-slot";
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
                <Dialog.Overlay className="custom-scroll bg-overlay dim:bg-overlay-dim dark:bg-overlay-dark fixed inset-0 z-50 grid place-items-start overflow-y-auto sm:place-items-center md:px-8 md:py-10">
                    <Dialog.Content className="dark:bg-theme-dark-900 relative w-full max-w-2xl bg-white sm:m-auto sm:mx-auto sm:max-w-[448px] sm:rounded-xl sm:shadow-2xl">
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
    const { t } = useTranslation();

    return (
        <div
            className={twMerge(
                "border-theme-secondary-300 dark:border-theme-dark-700 flex items-start justify-between border-b px-6 pt-4 pb-[0.875rem] sm:pt-[0.875rem]",
                className,
            )}
            {...props}
        >
            <Dialog.Title className="dark:text-theme-dark-50 m-0 text-left text-lg font-semibold">
                {children}
            </Dialog.Title>

            {!hideCloseButton && (
                <button
                    type="button"
                    onClick={onClose}
                    aria-label={t("actions.close")}
                    className="button button-secondary text-theme-secondary-700 dim:bg-transparent dim:shadow-none dark:text-theme-dark-200 hover:dark:bg-theme-dark-blue-600 hover:dark:text-theme-dark-50 m-0 h-6 w-6 shrink-0 rounded-none bg-transparent p-0! sm:rounded dark:bg-transparent dark:shadow-none"
                >
                    <CrossIcon className="m-auto h-4 w-4 fill-current" />
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
                "text-theme-secondary-700 dark:text-theme-dark-200 px-6 pt-4 pb-4 font-normal sm:pb-6",
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
            "border-theme-secondary-300 dark:border-theme-dark-700 mb-4 flex flex-col-reverse border-t px-6 pt-4 sm:flex-row sm:justify-end sm:space-x-3",
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

const ModalActionButton = ({
    className,
    asChild,
    ...props
}: React.HTMLAttributes<HTMLElement> & { asChild?: boolean }) => {
    const Comp = asChild ? Slot : "button";

    return (
        <Comp
            type="button"
            className={twMerge(
                "button button-primary flex items-center justify-center sm:mb-0 sm:px-4! sm:py-1.5",
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
