'use client';

import ConfigContext from "./ConfigContext";
import { Currencies, IConfigProductivity, INetwork, ISettings } from "@/types";

export default function ConfigProvider({
    currencies,
    network,
    productivity,
    settings,
    children,
}: {
    currencies: Currencies;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    children: React.ReactNode;
}) {
    const value = {
        currencies,
        network,
        productivity,
        settings,
    };

    return (
        <ConfigContext.Provider value={value}>
            {children}
        </ConfigContext.Provider>
    );
};
