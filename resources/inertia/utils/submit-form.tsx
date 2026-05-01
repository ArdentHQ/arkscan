import useToast from "@/Providers/Toast/useToast";
import { router, useHttp } from "@inertiajs/react";

interface UseSubmitFormOptions {
    route: string;
    formRef: React.RefObject<HTMLFormElement | null>;
    setErrors?: React.Dispatch<React.SetStateAction<Record<string, string>>>;
    onSuccess?: (response: unknown) => void;
    onFinish?: () => void;
}

export default function useSubmitForm({ route, formRef, setErrors, onSuccess, onFinish }: UseSubmitFormOptions) {
    const { addToast } = useToast();
    const http = useHttp<Record<string, string>>({});

    http.transform(() => {
        if (!formRef.current) {
            return {};
        }

        return Object.fromEntries(new FormData(formRef.current)) as Record<string, string>;
    });

    const submit = () => {
        setErrors?.({});

        http.post(route, {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            onSuccess: (response) => {
                formRef.current?.reset();

                router.reload({
                    only: ["flash"],
                });

                onSuccess?.(response);
            },
            onError: (errors) => {
                setErrors?.(errors as Record<string, string>);
            },
            onHttpException: (response) => {
                if (response.status === 422) {
                    return;
                }

                type MessagePayload = { message?: string };
                let parsed: MessagePayload | null = null;
                try {
                    parsed = JSON.parse(response.data) as MessagePayload;
                } catch (e) {
                    parsed = null;
                }

                if (parsed?.message) {
                    addToast(parsed.message, { type: "error" });

                    return;
                }

                addToast(`Request failed with status ${response.status}`, { type: "error" });

                router.reload({
                    only: ["flash"],
                });
            },
            onNetworkError: (error) => {
                addToast(error.message, { type: "error" });

                router.reload({
                    only: ["flash"],
                });
            },
            onFinish: () => {
                onFinish?.();
            },
        });
    };

    return {
        submit,
        errors: http.errors,
        processing: http.processing,
    };
}
