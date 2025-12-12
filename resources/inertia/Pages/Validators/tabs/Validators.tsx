import ValidatorsTableWrapper from "@/Components/Tables/Desktop/Validators/Validators";
import ValidatorsMobileTableWrapper from "@/Components/Tables/Mobile/Validators/Validators";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { useTranslation } from "react-i18next";

export default function ValidatorsTab({ validators, filters }: Pick<ValidatorsProps, "validators" | "filters">) {
    const { t } = useTranslation();

    filters;

    return (
        <FilterProvider
            initialOptions={[
                {
                    label: t("tables.filters.validators.active"),
                    value: "active",
                    selected: filters.validators.active,
                },
                {
                    label: t("tables.filters.validators.standby"),
                    value: "standby",
                    selected: filters.validators.standby,
                },
                {
                    label: t("tables.filters.validators.dormant"),
                    value: "dormant",
                    selected: filters.validators.dormant,
                },
                {
                    label: t("tables.filters.validators.resigned"),
                    value: "resigned",
                    selected: filters.validators.resigned,
                },
            ]}
            onChange={() => {}}
        >
            <ValidatorsTableWrapper
                validators={validators}
                mobile={<ValidatorsMobileTableWrapper validators={validators} />}
            />
        </FilterProvider>
    );
}
