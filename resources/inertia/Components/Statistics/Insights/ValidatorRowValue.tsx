import Number from "@/Components/General/Number";
import { formatDate } from "@/utils/formatter";
import { useTranslation } from "react-i18next";

export default function ValidatorRowValue({ rowKey, value }: { rowKey: string; value: number | string | null }) {
    const { t } = useTranslation();

    if (rowKey.includes("active_validator") && typeof value === "number") {
        return <>{formatDate(value)}</>;
    }

    if (typeof value === "number") {
        return <Number>{value}</Number>;
    }

    return <>{value ?? t("general.na")}</>;
}
