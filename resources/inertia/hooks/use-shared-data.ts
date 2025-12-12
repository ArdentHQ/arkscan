import { PageProps } from "@inertiajs/core";
import { usePage } from "@inertiajs/react";

const useShareData = <TProps extends object = {}>() => {
    const { props } = usePage<PageProps<TProps>>();

    return props;
};

export default useShareData;
