import ValidatorsTableWrapper from "@/Components/Tables/Desktop/Validators/Validators";
import ValidatorsMobileTableWrapper from "@/Components/Tables/Mobile/Validators/Validators";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { useTranslation } from "react-i18next";

export default function ValidatorsTab({ validators, filters }: Pick<ValidatorsProps, "validators" | "filters">) {
    const { t } = useTranslation();

    return (
        <FilterProvider
            initialOptions={[
                {
                    label: t("tables.filters.transactions.addressing"),
                    options: [
                        {
                            label: t("tables.filters.transactions.outgoing"),
                            value: "outgoing",
                            selected: filters.outgoing,
                        },
                        {
                            label: t("tables.filters.transactions.incoming"),
                            value: "incoming",
                            selected: filters.incoming,
                        },
                    ],
                },
                {
                    label: t("tables.filters.transactions.types"),
                    options: [
                        {
                            label: t("tables.filters.transactions.transfers"),
                            value: "transfers",
                            selected: filters.transfers,
                        },
                        {
                            label: t("tables.filters.transactions.multipayments"),
                            value: "multipayments",
                            selected: filters.multipayments,
                        },
                        {
                            label: t("tables.filters.transactions.votes"),
                            value: "votes",
                            selected: filters.votes,
                        },
                        {
                            label: t("tables.filters.transactions.validator"),
                            value: "validator",
                            selected: filters.validator,
                        },
                        {
                            label: t("tables.filters.transactions.username"),
                            value: "username",
                            selected: filters.username,
                        },
                        {
                            label: t("tables.filters.transactions.contract_deployment"),
                            value: "contract_deployment",
                            selected: filters.contract_deployment,
                        },
                        {
                            label: t("tables.filters.transactions.others"),
                            value: "others",
                            selected: filters.others,
                        },
                    ],
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
