import { useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import Input from "../Input/Input";
import BasicSelect from "../Input/BasicSelect";
import TextArea from "../Input/TextArea";
import { SubjectOption } from "@/Pages/Support.contracts";
import useSharedData from "@/hooks/use-shared-data";
import Honeypot from "../Input/Includes/Honeypot";
import useToast from "@/Providers/Toast/useToast";
import submitForm from "@/utils/submit-form";

export default function SupportForm({ subjects }: { subjects: SubjectOption[] }) {
    const { t } = useTranslation();

    const formRef = useRef<HTMLFormElement>(null);

    const { errors: pageErrors } = useSharedData();
    const [errors, setErrors] = useState<Record<string, string>>(pageErrors);
    const [canSubmit, setCanSubmit] = useState(true);

    const { addToast } = useToast();

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        setErrors({});
        setCanSubmit(false);

        submitForm({
            route: route("contact"),
            formRef,
            setErrors,
            addToast,
            onFinish: () => {
                setCanSubmit(true);
            },
        });
    };

    return (
        <div className="mt-6 flex flex-1 flex-col rounded-xl border-theme-secondary-300 px-6 dark:border-theme-dark-700 md:mt-0 md:mt-3 md:border md:py-6 lg:ml-1.5 lg:mt-0">
            <div className="mb-2 font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-lg">
                {t("pages.support.form.title")}
            </div>

            <div>{t("pages.contact.form.description", { ns: "ui" })}</div>

            <form ref={formRef} id="contact-form" className="flex flex-1 flex-col space-y-3" onSubmit={onSubmit}>
                <Honeypot />

                <div className="flex flex-col space-y-3 md-lg:flex-row md-lg:space-x-3 md-lg:space-y-0 lg:flex-col lg:space-x-0 lg:space-y-3">
                    <div className="flex flex-col space-y-3 md:flex-row md:space-x-3 md:space-y-0 md-lg:flex-2 lg:flex-1">
                        <Input
                            name="name"
                            label={t("forms.name", { ns: "ui" })}
                            autoComplete="name"
                            className="flex-1"
                            inputClass="h-14"
                            error={errors["name"]}
                            testId="contact:form:name"
                        />

                        <Input
                            type="email"
                            name="email"
                            label={t("forms.email", { ns: "ui" })}
                            autoComplete="email"
                            className="flex-1"
                            inputClass="h-14"
                            error={errors["email"]}
                            testId="contact:form:email"
                        />
                    </div>

                    <BasicSelect
                        name="subject"
                        label={t("forms.subject", { ns: "ui" })}
                        error={errors["subject"]}
                        inputClass="h-14"
                        testId="contact:form:subject"
                    >
                        {subjects.map(({ value, label }, index) => (
                            <option key={index} value={value}>
                                {label}
                            </option>
                        ))}
                    </BasicSelect>
                </div>

                <TextArea
                    name="message"
                    label={t("forms.message", { ns: "ui" })}
                    rows={2}
                    className="w-full"
                    placeholder={t("pages.contact.message_placeholder", { ns: "ui" })}
                    error={errors["message"]}
                    testId="contact:form:message"
                />

                <div className="relative flex flex-1 flex-col justify-end pt-1">
                    <button type="submit" className="button-primary" disabled={!canSubmit}>
                        {t("actions.send", { ns: "ui" })}
                    </button>
                </div>
            </form>
        </div>
    );
}
