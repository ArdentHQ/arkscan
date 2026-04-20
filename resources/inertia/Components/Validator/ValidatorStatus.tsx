import { IValidator } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { useMemo } from "react";
import Badge from "@/Components/General/Badge";

export default function ValidatorStatus({ validator }: { validator: IValidator }) {
    const { t } = useTranslation();

    const statusLabel = useMemo(() => {
        if (validator.isActive) {
            return t("general.validators.forging-status.active");
        }

        if (validator.isResigned) {
            return t("general.validators.forging-status.resigned");
        }

        if (validator.isDormant) {
            return t("general.validators.forging-status.dormant");
        }

        return t("general.validators.forging-status.standby");
    }, [validator.isActive, validator.isResigned, validator.isDormant, t]);

    return <Badge className="encapsulated-badge">{statusLabel}</Badge>;
}
