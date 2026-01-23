import { createContext } from "react";
import { ToastContextValue } from "./types";

const ToastContext = createContext<ToastContextValue | null>(null);

export default ToastContext;
