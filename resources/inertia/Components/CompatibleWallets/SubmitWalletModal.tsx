import Modal from "@/Components/General/Modal";
import { useTranslation } from "react-i18next";
import Input from "../Input/Input";
import TextArea from "../Input/TextArea";
import useSharedData from "@/hooks/use-shared-data";
import { useRef, useState } from "react";
import useToast from "@/Providers/Toast/useToast";
import submitForm from "@/utils/submit-form";

interface SubmitWalletModalProps {
    isOpen: boolean;
    onClose: () => void;
    close: () => void;
}

export default function SubmitWalletModal({ isOpen, onClose, close }: SubmitWalletModalProps) {
    const { t } = useTranslation();

    const formRef = useRef<HTMLFormElement>(null);

    const { errors: pageErrors } = useSharedData();
    const [errors, setErrors] = useState<Record<string, string>>(pageErrors);

    const [isFormValid, setIsFormValid] = useState(false);
    const { addToast } = useToast();

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        setIsFormValid(false);
        setErrors({});

        submitForm({
            route: route("compatible-wallets.submit"),
            formRef,
            setErrors,
            addToast,
            onSuccess: () => {
                close();

                validateFields();
            },
        });
    };

    const validateFields = () => {
        const formData = new FormData(formRef.current!);
        const name = formData.get("name") as string;
        const website = formData.get("website") as string;

        if (name.trim() !== "" && website.trim() !== "") {
            setIsFormValid(true);

            return;
        }

        setIsFormValid(false);
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} description="Export Table">
            <Modal.Title>{t("pages.compatible-wallets.submit-modal.title")}</Modal.Title>

            <Modal.Body>
                <form ref={formRef} onSubmit={onSubmit} className="space-y-5">
                    <Input
                        label={t("pages.compatible-wallets.submit-modal.name")}
                        name="name"
                        autoComplete="no"
                        onChange={validateFields}
                        error={errors.name}
                    />

                    <Input
                        label={t("pages.compatible-wallets.submit-modal.website")}
                        name="website"
                        autoComplete="no"
                        placeholder={t("pages.compatible-wallets.submit-modal.website_placeholder")}
                        onChange={validateFields}
                        error={errors.website}
                    />

                    <TextArea
                        label={t("pages.compatible-wallets.submit-modal.message")}
                        name="message"
                        autoComplete="no"
                        rows={4}
                        error={errors.message}
                    />
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
                            disabled={!isFormValid}
                            onClick={() => formRef.current?.requestSubmit()}
                        >
                            {t("actions.submit")}
                        </Modal.ActionButton>
                    </>
                </Modal.FooterButtons>
            </Modal.Footer>
        </Modal>
    );
}
