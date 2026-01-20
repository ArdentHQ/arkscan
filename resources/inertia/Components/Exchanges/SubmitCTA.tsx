import { useRef, useState } from "react";
import SubmitCTA from "../Resources/SubmitCTA";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { ExchangesProps } from "@/Pages/Exchanges.contracts";
import Input from "../Input/Input";
import TextArea from "../Input/TextArea";
import SubmitModal from "../Resources/SubmitModal";

export default function ExchangesSubmitCTA() {
    const { t } = useTranslation();
    const formRef = useRef<HTMLFormElement>(null);

    const { errors } = useSharedData<ExchangesProps>();

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isFormValid, setIsFormValid] = useState(false);

    const validateFields = () => {
        const formData = new FormData(formRef.current!);
        const name = formData.get("name") as string;
        const website = formData.get("website") as string;
        const pairs = formData.get("pairs") as string;

        if (name.trim() !== "" && website.trim() !== "" && pairs.trim() !== "") {
            setIsFormValid(true);

            return;
        }

        setIsFormValid(false);
    };

    return (
        <>
            <SubmitCTA
                title={t("pages.exchanges.dont_see_an_exchange")}
                subtitle={t("pages.exchanges.let_us_know")}
                button={t("actions.submit_exchange")}
                onClick={() => setIsModalOpen(true)}
            />

            <SubmitModal
                ref={formRef}
                route={route("exchanges.submit")}
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                onSubmit={() => setIsFormValid(false)}
                validateFields={validateFields}
                disabled={!isFormValid}
            >
                <Input
                    label={t("pages.exchanges.submit-modal.name")}
                    name="name"
                    autoComplete="no"
                    onChange={validateFields}
                    error={errors.name}
                />

                <Input
                    label={t("pages.exchanges.submit-modal.website")}
                    name="website"
                    autoComplete="no"
                    placeholder={t("pages.exchanges.submit-modal.website_placeholder")}
                    onChange={validateFields}
                    error={errors.website}
                />

                <Input
                    label={t("pages.exchanges.submit-modal.pairs")}
                    name="pairs"
                    autoComplete="no"
                    placeholder={t("pages.exchanges.submit-modal.pairs_placeholder")}
                    onChange={validateFields}
                    error={errors.pairs}
                />

                <TextArea
                    label={t("pages.exchanges.submit-modal.message")}
                    name="message"
                    autoComplete="no"
                    rows={4}
                    error={errors.message}
                />
            </SubmitModal>
        </>
    );
}
