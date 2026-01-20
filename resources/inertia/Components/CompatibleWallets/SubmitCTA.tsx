import { useRef, useState } from "react";
import SubmitCTA from "../Resources/SubmitCTA";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { CompatibleWalletsProps } from "@/Pages/CompatibleWallets.contracts";
import Input from "../Input/Input";
import TextArea from "../Input/TextArea";
import SubmitModal from "../Resources/SubmitModal";

export default function CompatibleWalletsSubmitCTA() {
    const { t } = useTranslation();
    const formRef = useRef<HTMLFormElement>(null);

    const { errors } = useSharedData<CompatibleWalletsProps>();

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isFormValid, setIsFormValid] = useState(false);

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
        <>
            <SubmitCTA
                title={t("pages.compatible-wallets.dont_see_a_wallet")}
                subtitle={t("pages.compatible-wallets.let_us_know")}
                button={t("actions.submit_wallet")}
                onClick={() => setIsModalOpen(true)}
            />

            <SubmitModal
                ref={formRef}
                route={route("compatible-wallets.submit")}
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                onSubmit={() => setIsFormValid(false)}
                validateFields={validateFields}
                disabled={!isFormValid}
            >
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
            </SubmitModal>
        </>
    );
}
