"use client";

import { PageProps } from "@inertiajs/core";
import ConfigContext from "./ConfigContext";

interface ConfigProviderProps extends PageProps {
    children: React.ReactNode;
}

export default function ConfigProvider({
    children,
    ...props
}: ConfigProviderProps) {
    return (
        <ConfigContext.Provider value={props}>
            {children}
        </ConfigContext.Provider>
    );
}
