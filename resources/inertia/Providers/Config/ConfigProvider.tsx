'use client';

import ConfigContext from "./ConfigContext";
import { Currencies, IConfigArkConnect, IConfigProductivity, INetwork, ISettings } from "@/types";

export default function ConfigProvider({
    arkconnect,
    currencies,
    network,
    productivity,
    settings,
    children,
}: {
    arkconnect: IConfigArkConnect;
    currencies: Currencies;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    children: React.ReactNode;
}) {
    const value = {
        arkconnect,
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
