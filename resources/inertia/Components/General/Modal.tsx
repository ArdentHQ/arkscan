import * as Dialog from "@radix-ui/react-dialog";
import classNames from "@/utils/class-names";

interface ModalProps extends React.PropsWithChildren {
    isOpen: boolean;
    onClose: () => void;
    title?: React.ReactNode;
    description?: React.ReactNode;
    footer?: React.ReactNode;
}

export default function Modal({ isOpen, onClose, title, description, footer, children }: ModalProps) {
    return (
        <Dialog.Root open={isOpen} onOpenChange={onClose}>
            <Dialog.Portal>
                <Dialog.Overlay className="bg-theme-secondary-900/75 dim:bg-theme-dark-950/50 dark:bg-theme-secondary-800/50 fixed inset-0 z-50 grid place-items-start overflow-y-auto sm:place-items-center md:px-8 md:py-10">
                    <Dialog.Content className="custom-scroll relative w-full max-w-2xl bg-white dark:bg-theme-dark-900 sm:m-auto sm:mx-auto sm:max-w-[448px] sm:rounded-xl sm:shadow-2xl">
                        {/* Header */}
                        {title && (
                            <div className="border-b border-theme-secondary-300 px-6 py-4 dark:border-theme-dark-700">
                                <Dialog.Title className="text-lg font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                                    {title}
                                </Dialog.Title>
                            </div>
                        )}

                        {/* Description (for accessibility) */}
                        {description && <Dialog.Description className="sr-only">{description}</Dialog.Description>}

                        {/* Content */}
                        <div className="px-6 py-4">{children}</div>

                        {/* Footer */}
                        {footer && (
                            <div className="border-t border-theme-secondary-300 px-6 py-4 dark:border-theme-dark-700">
                                {footer}
                            </div>
                        )}
                    </Dialog.Content>
                </Dialog.Overlay>
            </Dialog.Portal>
        </Dialog.Root>
    );
}
