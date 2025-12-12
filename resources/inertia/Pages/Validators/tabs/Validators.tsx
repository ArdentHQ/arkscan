import ValidatorsTableWrapper from "@/Components/Tables/Desktop/Validators/Validators";
import ValidatorsMobileTableWrapper from "@/Components/Tables/Mobile/Validators/Validators";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { useTranslation } from "react-i18next";

export default function ValidatorsTab({ validators, filters }: Pick<ValidatorsProps, "validators" | "filters">) {
    const { t } = useTranslation();

    return (
        <FilterProvider
            initialOptions={
                [
                    // @TODO: implement validators filters logic https://app.clickup.com/t/86dyqe7cg
                ]
            }
            onChange={() => {}}
        >
            <ValidatorsTableWrapper
                validators={validators}
                mobile={<ValidatorsMobileTableWrapper validators={validators} />}
            />
        </FilterProvider>
    );
}
