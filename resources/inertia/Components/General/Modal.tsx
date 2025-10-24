import * as Dialog from "@radix-ui/react-dialog";
import CrossIcon from "@ui/icons/cross.svg?react";

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
                <Dialog.Overlay className="dark:bg-theme-secondary-800/50 fixed inset-0 z-50 bg-theme-secondary-900 opacity-75 dim:bg-theme-dark-950 dark:opacity-50" />

                <div className="fixed inset-0 z-50 grid place-items-start overflow-y-auto sm:place-items-center md:px-8 md:py-10">
                    <Dialog.Content className="custom-scroll relative w-full max-w-2xl bg-white dark:bg-theme-dark-900 sm:m-auto sm:mx-auto sm:max-w-[448px] sm:rounded-xl sm:shadow-2xl">
                        {/* Header */}
                        {title && (
                            <div className="flex items-center justify-between border-b border-theme-secondary-300 px-6 pb-[0.875rem] pt-4 dark:border-theme-dark-700 sm:pb-4 sm:pt-[0.875rem]">
                                <Dialog.Title className="m-0 text-left text-lg font-semibold dark:text-theme-dark-50">
                                    {title}
                                </Dialog.Title>

                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="button button-secondary m-0 h-6 w-6 rounded-none bg-transparent p-0 text-theme-secondary-700 dim:bg-transparent dim:shadow-none dark:bg-transparent dark:text-theme-dark-200 dark:shadow-none hover:dark:bg-theme-dark-blue-600 hover:dark:text-theme-dark-50 sm:rounded"
                                >
                                    <CrossIcon className="fill-current m-auto h-4 w-4" />
                                </button>
                            </div>
                        )}

                        {description && <Dialog.Description className="sr-only">{description}</Dialog.Description>}

                        {/* Content */}
                        <div className="px-6 pb-4 pt-4 font-normal text-theme-secondary-700 dark:text-theme-dark-200 sm:pb-6">
                            {children}
                        </div>

                        {footer && (
                            <div className="mb-4 flex flex-col-reverse border-t border-theme-secondary-300 px-6 pt-4 text-right dark:border-theme-dark-700 sm:flex-row sm:justify-end sm:space-x-3">
                                {footer}
                            </div>
                        )}
                    </Dialog.Content>
                </div>
            </Dialog.Portal>
        </Dialog.Root>
    );
}
