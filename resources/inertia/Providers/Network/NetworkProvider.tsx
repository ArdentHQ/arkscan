'use client';

import NetworkContext from "./NetworkContext";
import { INetwork } from "@/types";

export default function NetworkProvider({
    network,
    children,
}: {
    network: INetwork;
    children: React.ReactNode;
}) {
    return (
        <NetworkContext.Provider value={{network}}>
            {children}
        </NetworkContext.Provider>
    );
};
