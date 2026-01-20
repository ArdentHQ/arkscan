import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { ExchangesProps } from "@/Pages/Exchanges.contracts";
import { useEffect, useState } from "react";
import { router } from "@inertiajs/react";
import ExchangeDropdown from "./Dropdown";
import StackIcon from "@icons/stack.svg?react";
import PairIcon from "@icons/pair.svg?react";

export default function ExchangeTableFilters() {
    const { t } = useTranslation();

    const { typeOptions, pairOptions } = useSharedData<ExchangesProps>();

    const [selectedType, setSelectedType] = useState<string>("all");
    const [selectedPair, setSelectedPair] = useState<string>("all");

    const updateData = () => {
        router.reload({
            only: ["exchanges"],
            data: {
                type: selectedType,
                pair: selectedPair,
            },
        });
    };

    const updateType = (type: string, reloadData: boolean = true) => {
        setSelectedType(type);

        if (reloadData) {
            updateData();
        }
    };

    const updatePair = (pair: string, reloadData: boolean = true) => {
        setSelectedPair(pair);

        if (reloadData) {
            updateData();
        }
    };

    useEffect(() => {
        const urlParams = new URL(location.href).searchParams;

        if (urlParams.get("type")) {
            updateType(urlParams.get("type") as string, false);
        }

        if (urlParams.get("pair")) {
            updatePair(urlParams.get("pair") as string, false);
        }
    }, []);

    return (
        <div className="flex w-full flex-col space-y-2 sm:flex-row sm:space-x-3 sm:space-y-0 md-lg:w-auto">
            <ExchangeDropdown icon={StackIcon} items={typeOptions} onChange={(value: string) => updateType(value)}>
                {t("pages.exchanges.type." + selectedType)}
            </ExchangeDropdown>

            <ExchangeDropdown icon={PairIcon} items={pairOptions} onChange={(value: string) => updatePair(value)}>
                {t("pages.exchanges.pair." + selectedPair)}
            </ExchangeDropdown>
        </div>
    );
}
