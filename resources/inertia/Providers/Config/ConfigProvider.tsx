"use client";

import ConfigContext from "./ConfigContext";
import { PageProps } from "@inertiajs/core";

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
