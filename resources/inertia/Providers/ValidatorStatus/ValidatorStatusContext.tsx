'use client';

import { createContext, useContext } from "react";
import { ValidatorStatusContextType } from "./types";

const ValidatorStatusContext = createContext<ValidatorStatusContextType | null>(null);

export function useValidatorStatus() {
    const context = useContext(ValidatorStatusContext);
    if (! context) {
        throw new Error("useValidatorStatus must be used within a ValidatorStatusProvider");
    }

    return context;
}

export default ValidatorStatusContext;
