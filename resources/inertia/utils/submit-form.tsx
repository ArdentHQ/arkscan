import useToast from "@/Providers/Toast/useToast";
import { router } from "@inertiajs/react";
import axios, { AxiosResponse } from "axios";

export default function submitForm({
    route,
    formRef,
    setErrors,
    addToast,
    onSuccess,
    onFinish,
}: {
    route: string;
    formRef: React.RefObject<HTMLFormElement | null>;
    setErrors: React.Dispatch<React.SetStateAction<Record<string, string>>>;
    addToast: ReturnType<typeof useToast>["addToast"];
    onSuccess?: (response: AxiosResponse) => void;
    onFinish?: () => void;
}) {
    axios
        .post(
            route,
            new FormData(formRef.current!).entries().reduce(
                (acc: Record<string, string>, [key, value]: [string, FormDataEntryValue]) => {
                    acc[key] = value.toString();

                    return acc;
                },
                {} as Record<string, string>,
            ),
            {
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            },
        )
        .then((response) => {
            formRef.current?.reset();

            router.reload();

            onSuccess?.(response);
        })
        .catch((e) => {
            if (e.response?.data?.errors) {
                setErrors(e.response.data.errors);
            }

            if (e.response?.data?.message) {
                addToast(e.response.data.message, {
                    type: "error",
                });

                return;
            }

            addToast(e.message, {
                type: "error",
            });

            router.reload();
        })
        .finally(() => {
            onFinish?.();
        });
}
