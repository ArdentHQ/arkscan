import { useEffect, useRef } from "react";
import useSharedData from "@/hooks/use-shared-data";
import useToast from "./useToast";
import { ToastType } from "./types";

type FlashMessage = {
    message: string;
    type?: ToastType;
};

export default function FlashToastListener() {
    const { flash } = useSharedData<{ flash?: FlashMessage | null }>();
    const { addToast } = useToast();
    const lastFlash = useRef<FlashMessage | null>(null);

    useEffect(() => {
        if (!flash?.message) {
            lastFlash.current = null;
            return;
        }

        if (lastFlash.current === flash) {
            return;
        }

        lastFlash.current = flash;

        addToast(flash.message, { type: flash.type ?? "info", html: true });
    }, [addToast, flash]);

    return null;
}
