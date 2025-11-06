import { useTranslation } from "react-i18next";
import MultiSelect from "@/Components/General/MultiSelect";
import MultiSelectTags from "@/Components/General/MultiSelectTags";
import useConfig from "@/hooks/use-config";

interface BlockColumnsSelectProps {
    value: string[];
    onValueChange: (values: string[]) => void;
}

export default function BlockColumnsSelect({ value, onValueChange }: BlockColumnsSelectProps) {
    const { t } = useTranslation();
    const { network, settings } = useConfig();

    const allColumnValues = [
        "id",
        "timestamp",
        "numberOfTransactions",
        "volume",
        "volumeFiat",
        "total",
        "totalFiat",
        "rate",
    ];

    const canBeExchanged = network?.canBeExchanged ?? false;

    const filteredColumns = allColumnValues.filter((columnKey) => {
        if (!canBeExchanged && ["volumeFiat", "totalFiat", "rate"].includes(columnKey)) {
            return false;
        }

        return true;
    });

    const handleRemoveColumn = (columnValue: string) => {
        onValueChange(value.filter((v) => v !== columnValue));
    };

    const columnLabel = (columnKey: string) => {
        return t(`pages.wallet.export-blocks-modal.columns-options.${columnKey}`, {
            networkCurrency: network?.currency ?? "",
            userCurrency: settings?.currency ?? "",
        });
    };

    const selectedText = () => {
        const count = value.length;

        if (count === 0) {
            return null;
        }

        if (count === filteredColumns.length) {
            return `(${count}) ${t("general.all")} ${t("pages.wallet.export-blocks-modal.columns_x_selected.plural")}`;
        }

        const label =
            count === 1
                ? t("pages.wallet.export-blocks-modal.columns_x_selected.singular")
                : t("pages.wallet.export-blocks-modal.columns_x_selected.plural");

        return `(${count}) ${label}`;
    };

    const showTags = value.length > 0 && value.length < filteredColumns.length;

    return (
        <>
            <MultiSelect value={value} onValueChange={onValueChange}>
                <MultiSelect.Trigger
                    data-testid="wallet:blocks-export:columns-trigger"
                    placeholder={t("pages.wallet.export-blocks-modal.columns_placeholder")}
                >
                    {selectedText()}
                </MultiSelect.Trigger>

                <MultiSelect.Content className="-mx-6 w-screen sm:mx-0 sm:w-100">
                    <MultiSelect.AllItem
                        data-testid="wallet:blocks-export:columns-select-all"
                        allValues={filteredColumns}
                    >
                        {t("general.select_all")} {t("pages.wallet.export-blocks-modal.columns")}
                    </MultiSelect.AllItem>

                    {filteredColumns.map((columnKey) => (
                        <MultiSelect.Item
                            key={columnKey}
                            data-testid={`wallet:blocks-export:column-${columnKey}`}
                            value={columnKey}
                        >
                            {columnLabel(columnKey)}
                        </MultiSelect.Item>
                    ))}
                </MultiSelect.Content>
            </MultiSelect>

            {showTags && (
                <MultiSelectTags>
                    {value.map((columnKey) => (
                        <MultiSelectTags.Tag key={columnKey} value={columnKey} onRemove={handleRemoveColumn}>
                            {columnLabel(columnKey)}
                        </MultiSelectTags.Tag>
                    ))}
                </MultiSelectTags>
            )}
        </>
    );
}
