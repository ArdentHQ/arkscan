import { useTranslation } from "react-i18next";
import MultiSelect from "@/Components/General/MultiSelect";

interface TransactionTypesSelectProps {
    value: string[];
    onValueChange: (values: string[]) => void;
}

export default function TransactionTypesSelect({ value, onValueChange }: TransactionTypesSelectProps) {
    const { t } = useTranslation();

    const getSelectedTypesText = () => {
        const count = value.length;
        if (count === 0) return null;

        const totalTypes = 4; // transfers, votes, multipayments, others
        if (count === totalTypes) {
            return `(${count}) ${t("general.all")} ${t("pages.wallet.export-transactions-modal.types_x_selected.plural")}`;
        }

        const typeLabel =
            count === 1
                ? t("pages.wallet.export-transactions-modal.types_x_selected.singular")
                : t("pages.wallet.export-transactions-modal.types_x_selected.plural");

        return `(${count}) ${typeLabel}`;
    };

    const allTypeValues = ["transfers", "votes", "multipayments", "others"];

    return (
        <MultiSelect value={value} onValueChange={onValueChange}>
            <MultiSelect.Trigger placeholder={t("pages.wallet.export-transactions-modal.types_placeholder")}>
                {getSelectedTypesText()}
            </MultiSelect.Trigger>

            <MultiSelect.Content className="w-full sm:w-100">
                <MultiSelect.AllItem allValues={allTypeValues}>
                    {t("general.select_all")} {t("pages.wallet.export-transactions-modal.types")}
                </MultiSelect.AllItem>

                <MultiSelect.Item value="transfers">
                    {t("pages.wallet.export-transactions-modal.types-options.transfers")}
                </MultiSelect.Item>
                <MultiSelect.Item value="votes">
                    {t("pages.wallet.export-transactions-modal.types-options.votes")}
                </MultiSelect.Item>
                <MultiSelect.Item value="multipayments">
                    {t("pages.wallet.export-transactions-modal.types-options.multipayments")}
                </MultiSelect.Item>
                <MultiSelect.Item value="others">
                    {t("pages.wallet.export-transactions-modal.types-options.others")}
                </MultiSelect.Item>
            </MultiSelect.Content>
        </MultiSelect>
    );
}
