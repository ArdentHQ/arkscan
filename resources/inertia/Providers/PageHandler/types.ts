import { CancelToken } from "@inertiajs/core";

export interface IPageHandlerContextType {
    isLoading: boolean;
    setIsLoading: (loading: boolean) => void;
    refreshPage: (callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => void;
    setRefreshPage: CallableFunction;
}
