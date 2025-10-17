'use client';

import ConfigContext from "./ConfigContext";
import { Currencies, IConfigArkConnect, IConfigProductivity, INetwork, ISettings, IConfigPagination } from "@/types";

export default function ConfigProvider({
    arkconnect,
    currencies,
    network,
    productivity,
    settings,
    pagination,
    children,
}: {
    arkconnect: IConfigArkConnect;
    currencies: Currencies;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    pagination: IConfigPagination;
    children: React.ReactNode;
}) {
    const value = {
        arkconnect,
        currencies,
        network,
        productivity,
        settings,
        pagination,
    };

    return (
        <ConfigContext.Provider value={value}>
            {children}
        </ConfigContext.Provider>
    );
};
