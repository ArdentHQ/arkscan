import Modal from "@/Components/General/Modal";
import { useTranslation } from "react-i18next";
import React, { RefObject } from "react";
import useSubmitForm from "@/utils/submit-form";

interface SubmitModalProps {
    ref: RefObject<HTMLFormElement | null>;
    route: string;
    isOpen: boolean;
    onClose: () => void;
    onSubmit: (e: React.FormEvent) => void;
    validateFields: () => void;
    setErrors: React.Dispatch<React.SetStateAction<Record<string, string>>>;
    children: React.ReactNode;
    disabled: boolean;
}

export default function SubmitModal({
    ref,
    route,
    isOpen,
    onClose,
    onSubmit,
    validateFields,
    setErrors,
    children,
    disabled = false,
}: SubmitModalProps) {
    const { t } = useTranslation();

    const { submit } = useSubmitForm({
        route,
        formRef: ref,
        setErrors,
        onSuccess: () => {
            onClose();
        },
        onFinish: () => {
            validateFields();
        },
    });

    const onFormSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        onSubmit(e);

        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
            <Modal.Title>{t("pages.compatible-wallets.submit-modal.title")}</Modal.Title>

            <Modal.Body>
                <form ref={ref} onSubmit={onFormSubmit} className="space-y-5">
                    {children}
                </form>
            </Modal.Body>

            <Modal.Footer>
                <Modal.FooterButtons>
                    <>
                        <button type="button" className="button-secondary" onClick={onClose}>
                            {t("actions.cancel")}
                        </button>

                        <Modal.ActionButton
                            type="submit"
                            data-testid="compatible-wallets:listing-modal:submit"
                            className="space-x-2"
                            disabled={disabled}
                            onClick={() => ref.current?.requestSubmit()}
                        >
                            {t("actions.submit")}
                        </Modal.ActionButton>
                    </>
                </Modal.FooterButtons>
            </Modal.Footer>
        </Modal>
    );
}
