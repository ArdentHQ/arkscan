import Number from "@/Components/General/Number";
import { DATE_FORMAT } from "@/constants";
import dayjs from "dayjs";
import { useTranslation } from "react-i18next";

export default function ValidatorRowValue({ rowKey, value }: { rowKey: string; value: number | string | null }) {
    const { t } = useTranslation();

    if (rowKey.includes("active_validator") && typeof value === "number") {
        return <>{dayjs(value * 1000).format(DATE_FORMAT)}</>;
    }

    if (typeof value === "number") {
        return <Number>{value}</Number>;
    }

    return <>{value ?? t("general.na")}</>;
}
