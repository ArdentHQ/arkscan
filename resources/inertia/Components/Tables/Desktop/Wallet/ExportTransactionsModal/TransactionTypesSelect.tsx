import { useTranslation } from "react-i18next";
import MultiSelect from "@/Components/General/MultiSelect";
import MultiSelectTags from "@/Components/General/MultiSelectTags";

interface TransactionTypesSelectProps {
    value: string[];
    onValueChange: (values: string[]) => void;
}

export default function TransactionTypesSelect({ value, onValueChange }: TransactionTypesSelectProps) {
    const { t } = useTranslation();

    const allTypeValues = ["transfers", "votes", "multipayments", "others"];
    const totalTypes = allTypeValues.length;

    const getSelectedTypesText = () => {
        const count = value.length;
        if (count === 0) return null;

        if (count === totalTypes) {
            return `(${count}) ${t("general.all")} ${t("pages.wallet.export-transactions-modal.types_x_selected.plural")}`;
        }

        const typeLabel =
            count === 1
                ? t("pages.wallet.export-transactions-modal.types_x_selected.singular")
                : t("pages.wallet.export-transactions-modal.types_x_selected.plural");

        return `(${count}) ${typeLabel}`;
    };

    const handleRemoveType = (typeValue: string) => {
        onValueChange(value.filter((v) => v !== typeValue));
    };

    // Only show tags if not all types are selected
    const showTags = value.length > 0 && value.length < totalTypes;

    return (
        <>
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

            {showTags && (
                <MultiSelectTags>
                    {value.map((typeValue) => (
                        <MultiSelectTags.Tag key={typeValue} value={typeValue} onRemove={handleRemoveType}>
                            {t(`pages.wallet.export-transactions-modal.types-options.${typeValue}`)}
                        </MultiSelectTags.Tag>
                    ))}
                </MultiSelectTags>
            )}
        </>
    );
}
