import { useTranslation } from "react-i18next";
import MultiSelect from "@/Components/General/MultiSelect";
import MultiSelectTags from "@/Components/General/MultiSelectTags";
import useConfig from "@/hooks/use-config";

interface TransactionColumnsSelectProps {
    value: string[];
    onValueChange: (values: string[]) => void;
}

export default function TransactionColumnsSelect({ value, onValueChange }: TransactionColumnsSelectProps) {
    const { t } = useTranslation();
    const { network, settings } = useConfig();

    const allColumnValues = [
        "id",
        "timestamp",
        "sender",
        "recipient",
        "amount",
        "amountFiat",
        "fee",
        "feeFiat",
        "rate",
    ];

    // Filter columns based on whether network can be exchanged
    const canBeExchanged = network?.canBeExchanged ?? false;
    const filteredColumns = allColumnValues.filter((key) => {
        return canBeExchanged || !["amountFiat", "feeFiat", "rate"].includes(key);
    });

    const getSelectedColumnsText = () => {
        const count = value.length;
        if (count === 0) return null;

        if (count === filteredColumns.length) {
            return `(${count}) ${t("general.all")} ${t("pages.wallet.export-transactions-modal.columns_x_selected.plural")}`;
        }

        const columnLabel =
            count === 1
                ? t("pages.wallet.export-transactions-modal.columns_x_selected.singular")
                : t("pages.wallet.export-transactions-modal.columns_x_selected.plural");

        return `(${count}) ${columnLabel}`;
    };

    const handleRemoveColumn = (columnValue: string) => {
        onValueChange(value.filter((v) => v !== columnValue));
    };

    const getColumnLabel = (columnKey: string) => {
        return t(`pages.wallet.export-transactions-modal.columns-options.${columnKey}`, {
            networkCurrency: network?.currency || "",
            userCurrency: settings?.currency || "",
        });
    };

    // Only show tags if not all columns are selected
    const showTags = value.length > 0 && value.length < filteredColumns.length;

    return (
        <>
            <MultiSelect value={value} onValueChange={onValueChange}>
                <MultiSelect.Trigger
                    data-testid="wallet:transactions-export:columns-trigger"
                    placeholder={t("pages.wallet.export-transactions-modal.columns_placeholder")}
                >
                    {getSelectedColumnsText()}
                </MultiSelect.Trigger>

                <MultiSelect.Content className="-mx-6 w-screen sm:mx-0 sm:w-100">
                    <MultiSelect.AllItem
                        data-testid="wallet:transactions-export:columns-select-all"
                        allValues={filteredColumns}
                    >
                        {t("general.select_all")} {t("pages.wallet.export-transactions-modal.columns")}
                    </MultiSelect.AllItem>

                    {filteredColumns.map((columnKey) => (
                        <MultiSelect.Item
                            key={columnKey}
                            data-testid={`wallet:transactions-export:column-${columnKey}`}
                            value={columnKey}
                        >
                            {getColumnLabel(columnKey)}
                        </MultiSelect.Item>
                    ))}
                </MultiSelect.Content>
            </MultiSelect>

            {showTags && (
                <MultiSelectTags>
                    {value.map((columnKey) => (
                        <MultiSelectTags.Tag key={columnKey} value={columnKey} onRemove={handleRemoveColumn}>
                            {getColumnLabel(columnKey)}
                        </MultiSelectTags.Tag>
                    ))}
                </MultiSelectTags>
            )}
        </>
    );
}
