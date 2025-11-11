export interface IPageHandlerContextType {
    isLoading: boolean;
    setIsLoading: (loading: boolean) => void;
    refreshPage: (callback?: CallableFunction) => void;
    setRefreshPage: CallableFunction;
}
